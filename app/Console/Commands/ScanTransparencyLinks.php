<?php

namespace App\Console\Commands;

use App\Models\CompanyWebsite;
use App\Models\Document;
use App\Models\DocumentType;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ScanTransparencyLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scan-transparency-links {--limit=10 : Limit number of websites to scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan company websites with transparency dates and extract links from their transparency pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting transparency links scan...');

        $limit = $this->option('limit');

        // Get websites that have transparency_date but no url_trasparency
        $websites = CompanyWebsite::whereNotNull('transparency_date')
            ->whereNull('url_trasparency')
            ->orWhere('url_trasparency', '')
            ->limit($limit)
            ->get();

        $this->info('Found ' . $websites->count() . " websites to scan (limit: {$limit})");
        $this->newLine();

        foreach ($websites as $website) {
            $this->processWebsite($website);
        }

        $this->newLine();
        $this->info('Transparency links scan completed!');

        return 0;
    }

    /**
     * Process individual website and extract transparency links
     */
    private function processWebsite(CompanyWebsite $website): void
    {
        $this->line("Processing: {$website->domain}");
        $this->line("Transparency Date: {$website->transparency_date}");

        // Try to find transparency page
        $transparencyUrls = $this->findTransparencyUrls($website->domain);

        if (empty($transparencyUrls)) {
            $this->warn("  No transparency page found for {$website->domain}");
            $this->newLine();
            return;
        }

        foreach ($transparencyUrls as $transparencyUrl) {
            $this->line("  Found transparency page: {$transparencyUrl}");

            // Extract links from the transparency page
            $links = $this->extractLinksFromPage($transparencyUrl);

            if (empty($links)) {
                $this->warn('    No links found on transparency page');
                continue;
            }

            $this->info('    Found ' . count($links) . ' links:');

            // Create document records for each link
            foreach ($links as $link) {
                $this->line("      - {$link}");
                $this->createDocumentFromLink($link, $website, $transparencyUrl);
            }

            // Update the website with the first transparency URL found
            if (!$website->url_transparency) {
                $website->update(['url_transparency' => $transparencyUrl]);
                $this->info('    Updated website with transparency URL');
            }
        }

        $this->newLine();
    }

    /**
     * Create a Document record from a link
     */
    private function createDocumentFromLink(string $link, CompanyWebsite $website, string $transparencyUrl): void
    {
        try {
            // Get or create a document type for transparency documents
            $documentType = $this->getOrCreateTransparencyDocumentType();

            // Extract filename from URL
            $filename = basename(parse_url($link, PHP_URL_PATH));
            $name = $filename ?: 'Transparency Document';

            // Extract date from wp-content/uploads URL
            $emittedAt = $this->extractDateFromUrl($link) ?? $website->transparency_date;

            // Create document record
            $document = Document::create([
                'company_id' => $website->company_id,
                'documentable_id' => $website->id,
                'documentable_type' => CompanyWebsite::class,
                'document_type_id' => $documentType->id,
                'name' => $name,
                'description' => "Document found on transparency page: {$transparencyUrl}",
                'status' => 'uploaded',
                'url_document' => $link,  // Store the URL in the url_document field
                'emitted_at' => $emittedAt,
                'uploaded_by' => 1,  // System user ID, adjust as needed
            ]);

            $dateInfo = $emittedAt instanceof Carbon ? " (date: {$emittedAt->format('Y-m-d')})" : '';
            $this->info("        ✓ Created document: {$document->name} (ID: {$document->id}){$dateInfo}");
        } catch (\Exception $e) {
            $this->error("        ✗ Failed to create document for {$link}: " . $e->getMessage());
        }
    }

    /**
     * Extract year and month from wp-content/uploads URL and return Carbon date
     */
    private function extractDateFromUrl(string $url): ?Carbon
    {
        // Check if URL contains wp-content/uploads
        if (strpos($url, 'wp-content/uploads') === false) {
            return null;
        }

        // Extract year and month from URL pattern: /wp-content/uploads/YYYY/MM/filename
        $pattern = '/wp-content\/uploads\/(\d{4})\/(\d{2})\//';

        if (preg_match($pattern, $url, $matches)) {
            $year = $matches[1];
            $month = $matches[2];

            // Create Carbon date with first day of the month
            try {
                return Carbon::create($year, $month, 1);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Get or create a document type for transparency documents
     */
    private function getOrCreateTransparencyDocumentType(): DocumentType
    {
        $documentType = DocumentType::where('code', 'TRANSPARENCY_DOC')->first();

        if (!$documentType) {
            $documentType = DocumentType::create([
                'name' => 'Transparency Document',
                'code' => 'TRANSPARENCY_DOC',
                'is_person' => false,
                'is_signed' => false,
                'is_stored' => true,
                'is_practice' => false,
                'is_monitored' => true,
                'is_template' => false,
                'duration' => 365,  // 1 year validity
                'is_sensible' => false,
            ]);

            $this->info('        ✓ Created document type: Transparency Document');
        }

        return $documentType;
    }

    /**
     * Find possible transparency URLs for a domain
     */
    private function findTransparencyUrls(string $domain): array
    {
        $urls = [];

        // Ensure domain has protocol
        if (!str_starts_with($domain, 'http')) {
            $domain = 'https://' . $domain;
        }

        // Common transparency page paths
        $paths = [
            '/trasparenza',
            '/trasparenza/',
            '/transparency',
            '/transparency/',
            '/amministrazione-trasparente',
            '/amministrazione-trasparente/',
            '/privacy',
            '/privacy/',
            '/informative',
            '/informative/',
            '/legal',
            '/legal/',
            '/legal/privacy',
            '/legal/privacy/',
            '/informativa-privacy',
            '/informativa-privacy/',
            '/cookie-policy',
            '/cookie-policy/',
            '/footer',
            '/footer/',
        ];

        foreach ($paths as $path) {
            $url = rtrim($domain, '/') . $path;

            if ($this->urlExists($url)) {
                $urls[] = $url;
            }
        }

        return $urls;
    }

    /**
     * Check if URL exists and is accessible
     */
    private function urlExists(string $url): bool
    {
        try {
            $response = Http::timeout(10)->get($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract all links from a web page
     */
    private function extractLinksFromPage(string $url): array
    {
        try {
            $response = Http::timeout(15)->get($url);

            if (!$response->successful()) {
                return [];
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            $links = [];

            // Extract all href attributes from anchor tags
            $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links, $url) {
                $href = $node->attr('href');

                // Skip empty, javascript, mailto, tel links
                if (empty($href) ||
                        str_starts_with($href, 'javascript:') ||
                        str_starts_with($href, 'mailto:') ||
                        str_starts_with($href, 'tel:')) {
                    return;
                }

                // Convert relative URLs to absolute
                $absoluteUrl = $this->makeAbsoluteUrl($href, $url);

                // Only include links that point to files (common document extensions)
                if ($this->isFileLink($absoluteUrl)) {
                    $links[] = $absoluteUrl;
                }
            });

            // Remove duplicates and return
            return array_unique($links);
        } catch (\Exception $e) {
            $this->error("    Error extracting links from {$url}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Convert relative URL to absolute URL
     */
    private function makeAbsoluteUrl(string $href, string $baseUrl): string
    {
        // If already absolute, return as is
        if (str_starts_with($href, 'http')) {
            return $href;
        }

        $baseParts = parse_url($baseUrl);
        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? '';
        $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';

        $base = $scheme . '://' . $host . $port;

        // Handle root-relative URLs
        if (str_starts_with($href, '/')) {
            return $base . $href;
        }

        // Handle relative URLs
        $path = dirname($baseParts['path'] ?? '/');
        return $base . rtrim($path, '/') . '/' . ltrim($href, '/');
    }

    /**
     * Check if URL points to a file (document)
     */
    private function isFileLink(string $url): bool
    {
        $fileExtensions = [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'rtf', 'odt', 'ods', 'odp', 'zip', 'rar',
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp'
        ];

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, $fileExtensions);
    }
}
