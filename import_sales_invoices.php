<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\SalesInvoiceImportService;
use Illuminate\Support\Facades\Log;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the company ID
$companyId = 'd904fae6-702d-4965-95e5-667e066e46a8';

// File path
$filePath = 'public/Fatture acquisto reg. (2).xlsx';

echo "Starting import of sales invoices...\n";
echo "File: $filePath\n";
echo "Company ID: $companyId\n\n";

try {
    // Create import service instance
    $importService = new SalesInvoiceImportService();
    
    // Perform the import
    $results = $importService->import($filePath, $companyId);
    
    // Display results
    echo "Import completed!\n\n";
    echo "Results:\n";
    echo "- Imported: {$results['imported']}\n";
    echo "- Updated: {$results['updated']}\n";
    echo "- Skipped: {$results['skipped']}\n";
    echo "- Errors: {$results['errors']}\n";
    
    if (!empty($results['details'])) {
        echo "\nDetails:\n";
        foreach ($results['details'] as $detail) {
            echo "- $detail\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error during import: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
