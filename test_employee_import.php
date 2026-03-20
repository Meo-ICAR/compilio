<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Company;
use App\Services\EmployeeImportService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the import
$filePath = storage_path('app/private/employee-imports/01KM5Q1NG82JMW9Q1NM6HJRHYT.xlsx');

// Get or create a valid company
$company = Company::first();
if (!$company) {
    echo "Creating test company...\n";
    $company = Company::create([
        'name' => 'Test Company',
        'vat_number' => '12345678901',
        'type' => 'company'
    ]);
}

$companyId = $company->id;

echo "Testing Employee Import Service\n";
echo "File: $filePath\n";
echo "Company ID: $companyId ({$company->name})\n";
echo 'File exists: ' . (file_exists($filePath) ? 'YES' : 'NO') . "\n";
echo 'File size: ' . (file_exists($filePath) ? filesize($filePath) : 'N/A') . " bytes\n";
echo "\n";

try {
    $importService = new EmployeeImportService($companyId);
    $results = $importService->import($filePath);

    echo "Import Results:\n";
    echo 'Imported: ' . $results['imported'] . "\n";
    echo 'Updated: ' . $results['updated'] . "\n";
    echo 'Skipped: ' . $results['skipped'] . "\n";
    echo 'Errors: ' . $results['errors'] . "\n";

    if (!empty($results['details'])) {
        echo "\nDetails:\n";
        foreach ($results['details'] as $detail) {
            echo "- $detail\n";
        }
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
