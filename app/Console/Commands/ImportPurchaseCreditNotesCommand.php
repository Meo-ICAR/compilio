<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PurchaseCreditNoteImportService;
use Illuminate\Console\Command;

class ImportPurchaseCreditNotesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase-credit-notes:import {--company= : Company ID} {--file= : Excel file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import purchase credit notes from Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting purchase credit notes import...');

        // Get company
        $companyId = $this->option('company') ?: Company::first()->id;

        if ($companyId) {
            $company = Company::findOrFail($companyId);
        } else {
            $companies = Company::all();
            if ($companies->isEmpty()) {
                $this->error('No companies found');
                return 1;
            }

            $companyId = $this->choice('Select company', $companies->pluck('name', 'id')->toArray());
            $company = Company::findOrFail($companyId);
        }

        $this->info("Using company: {$company->name} (ID: {$company->id})");

        // Get file path
        $filePath = $this->option('file') ?? public_path('Note credito acquisto registrate.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Importing from: {$filePath}");

        // Perform import
        try {
            $filename = basename($filePath);
            $importService = new PurchaseCreditNoteImportService($company->id, $filename);
            $results = $importService->import($filePath, $company->id);

            // Display results
            $this->newLine();
            $this->info('Import Results:');
            $this->info('===============');
            $this->line("Imported: {$results['imported']}");
            $this->line("Updated: {$results['updated']}");
            $this->line("Skipped: {$results['skipped']}");
            $this->line("Errors: {$results['errors']}");

            if (!empty($results['details'])) {
                $this->newLine();
                $this->info('Details (first 10):');
                foreach (array_slice($results['details'], 0, 10) as $detail) {
                    $this->line("- {$detail}");
                }

                if (count($results['details']) > 10) {
                    $this->line('... and ' . (count($results['details']) - 10) . ' more details');
                }
            }

            $this->newLine();
            $this->info('✓ Import completed!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());
            return 1;
        }
    }
}
