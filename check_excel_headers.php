<?php

require_once __DIR__ . '/vendor/autoload.php';

use Maatwebsite\Excel\Facades\Excel;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = 'public/Fatture acquisto reg. (2).xlsx';

echo "Checking Excel file headers...\n";
echo "File: $filePath\n\n";

try {
    $data = Excel::toArray([], $filePath);
    
    if (empty($data) || empty($data[0])) {
        echo "Cannot read data from Excel file\n";
        exit;
    }
    
    $rows = $data[0];
    $headers = array_shift($rows);
    
    echo "Headers found in Excel file:\n";
    foreach ($headers as $index => $header) {
        echo "$index: " . var_export($header, true) . "\n";
    }
    
    echo "\nFirst few rows of data:\n";
    for ($i = 0; $i < min(3, count($rows)); $i++) {
        echo "Row " . ($i + 2) . ":\n";
        foreach ($rows[$i] as $index => $cell) {
            echo "  $index: " . var_export($cell, true) . "\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error reading Excel file: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
