<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\PurchaseInvoiceImportService;
use Illuminate\Support\Facades\Log;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the company ID
$companyId = 'd904fae6-702d-4965-95e5-667e066e46a8';

// File path
$filePath = 'public/Fatture acquisto reg. (2).xlsx';

echo "Starting import of purchase invoices...\n";
echo "File: $filePath\n";
echo "Company ID: $companyId\n\n";

try {
    // Create import service instance
    $importService = new PurchaseInvoiceImportService();
    
    // Perform the import
    $results = $importService->import($filePath, $companyId);
    
    // Display results
    echo "Import completed!\n\n";
    echo "Results:\n";
    echo "- Imported: {$results['imported']}\n";
    echo "- Updated: {$results['updated']}\n";
    echo "- Skipped: {$results['skipped']}\n";
    echo "- Errors: {$results['errors']}\n";
    echo "- Filename: {$results['filename']}\n";
    
    // Show first 10 and last 10 details
    if (!empty($results['details'])) {
        echo "\nFirst 10 details:\n";
        for ($i = 0; $i < min(10, count($results['details'])); $i++) {
            echo "- {$results['details'][$i]}\n";
        }
        
        if (count($results['details']) > 10) {
            echo "\nLast 10 details:\n";
            for ($i = max(0, count($results['details']) - 10); $i < count($results['details']); $i++) {
                echo "- {$results['details'][$i]}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error during import: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
