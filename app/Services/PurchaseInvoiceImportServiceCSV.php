<?php

namespace App\Services;

use App\Models\PurchaseInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseInvoiceImportServiceCSV
{
    protected $companyId;
    protected $filename;

    protected $importResults = [
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
        'details' => []
    ];

    public function setCompanyId($companyId): void
    {
        $this->companyId = $companyId;
    }

    public function __construct($filename = null)
    {
        $this->filename = $filename;
    }

    public function import($filePath, $companyId)
    {
        $this->companyId = $companyId;

        // Extract filename from path if not provided
        if (!$this->filename) {
            $this->filename = basename($filePath);
        }

        $this->importResults = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => [],
            'filename' => $this->filename,
        ];

        // Handle storage path
        $actualFilePath = $filePath;
        if (!file_exists($filePath)) {
            // Try storage path if public path doesn't exist
            $storagePath = str_replace('public/', 'storage/app/private/purchase-invoice-imports/', $filePath);
            if (file_exists($storagePath)) {
                $actualFilePath = $storagePath;
                Log::info('File found in storage path', ['path' => $storagePath]);
            } else {
                throw new \Exception("File not found: {$filePath} (tried: {$storagePath})");
            }
        }

        DB::beginTransaction();

        try {
            // Read CSV file using fopen for CSV format
            $handle = fopen($actualFilePath, 'r');
            if (!$handle) {
                throw new \Exception("Cannot open file: {$actualFilePath}");
            }

            // Read headers
            $headers = fgetcsv($handle, 0, ';');
            if (!$headers) {
                fclose($handle);
                throw new \Exception('Cannot read headers from file');
            }

            // Clean headers - remove special characters and normalize
            $cleanHeaders = [];
            foreach ($headers as $header) {
                $cleanHeader = trim($header);
                // Remove BOM if present
                $cleanHeader = str_replace("\u{FEFF}", '', $cleanHeader);
                $cleanHeader = str_replace(['.', ' ', '-', '(', ')', '/', '°'], ['_', '_', '_', '_', '_', '_', '_'], $cleanHeader);
                $cleanHeader = strtolower($cleanHeader);
                $cleanHeaders[] = $cleanHeader;
            }

            Log::info('Purchase Invoice CSV Headers', ['original' => $headers, 'cleaned' => $cleanHeaders]);

            $rowNumber = 2;  // Start from 2 since we already read header

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $this->processRow($row, $cleanHeaders, $rowNumber);
                $rowNumber++;
            }

            fclose($handle);

            DB::commit();

            Log::info('Purchase invoices import completed', [
                'file' => $filePath,
                'company_id' => $this->companyId,
                'results' => $this->importResults
            ]);

            return $this->importResults;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing purchase invoices', [
                'file' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['errors']++;
            $this->importResults['details'][] = 'Error processing file: ' . $e->getMessage();

            return $this->importResults;
        }
    }

    protected function processRow(array $row, array $headers, int $rowNumber)
    {
        try {
            // Use direct index mapping instead of array_combine for reliability
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }

            // Skip empty rows
            if (empty($rowData['nr_']) || empty($rowData['fornitore'])) {
                Log::info('Skipping row due to empty data', [
                    'row_number' => $rowNumber,
                    'nr_' => $rowData['nr_'] ?? 'NULL',
                    'fornitore' => $rowData['fornitore'] ?? 'NULL',
                    'raw_row' => $rowData
                ]);
                $this->importResults['skipped']++;
                return;
            }

            $invoiceData = $this->mapRowToInvoiceData($rowData);

            // Add company_id
            $invoiceData['company_id'] = $this->companyId;

            // Check if invoice already exists
            $existingInvoice = PurchaseInvoice::where('company_id', $this->companyId)
                ->where('number', $invoiceData['number'])
                ->first();

            if ($existingInvoice) {
                // Update existing invoice
                $existingInvoice->update($invoiceData);
                $this->importResults['updated']++;
                $this->importResults['details'][] = "Updated invoice: {$invoiceData['number']} (row {$rowNumber})";
            } else {
                // Create new invoice
                $invoice = PurchaseInvoice::create($invoiceData);
                $this->importResults['imported']++;
                $this->importResults['details'][] = "Imported invoice: {$invoiceData['number']} (row {$rowNumber})";
            }

        } catch (\Exception $e) {
            Log::error('Error processing row', [
                'row_number' => $rowNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['errors']++;
            $this->importResults['details'][] = "Error processing row {$rowNumber}: " . $e->getMessage();
        }
    }

    protected function mapRowToInvoiceData(array $row): array
    {
        return [
            'number' => $this->cleanString($row['nr_'] ?? null),
            'supplier_invoice_number' => $this->cleanString($row['nr__fatt__fornitore'] ?? null),
            'supplier_number' => $this->cleanString($row['nr__fornitore'] ?? null),
            'supplier' => $this->cleanString($row['fornitore'] ?? null),
            'currency_code' => $this->cleanString($row['cod__valuta'] ?? null),
            'amount' => $this->parseDecimal($row['importo'] ?? null),
            'amount_including_vat' => $this->parseDecimal($row['importo_iva_inclusa'] ?? null),
            'pay_to_cap' => $this->cleanString($row['pagare_a___cap'] ?? null),
            'pay_to_country_code' => $this->cleanString($row['pagare_a___cod__paese'] ?? null),
            'registration_date' => $this->parseDate($row['data_di_registrazione']) ?? now()->format('Y-m-d'),
            'location_code' => $this->cleanString($row['cod__ubicazione'] ?? null),
            'printed_copies' => $this->parseInteger($row['copie_stampate'] ?? 0),
            'document_date' => $this->parseDate($row['data_documento'] ?? null),
            'payment_condition_code' => $this->cleanString($row['cod__condizioni_pagam_'] ?? null),
            'due_date' => $this->parseDate($row['data_scadenza'] ?? null),
            'payment_method_code' => $this->cleanString($row['cod__metodo_di_pagamento'] ?? null),
            'residual_amount' => $this->parseDecimal($row['importo_residuo'] ?? null),
            'closed' => $this->parseBoolean($row['chiuso'] ?? null),
            'cancelled' => $this->parseBoolean($row['annullato'] ?? null),
            'corrected' => $this->parseBoolean($row['rettifica'] ?? null),
            'pay_to_address' => $this->cleanString($row['pagare_a___indirizzo'] ?? null),
            'pay_to_city' => $this->cleanString($row['pagare_a___città'] ?? null),
            'supplier_category' => $this->cleanString($row['cat__reg__fornitore'] ?? null),
            'exchange_rate' => $this->parseDecimal($row['fattore_valuta'] ?? null),
            'vat_number' => $this->cleanString($row['partita_iva'] ?? null),
            'fiscal_code' => $this->cleanString($row['codice_fiscale'] ?? null),
            'document_type' => $this->cleanString($row['tipo_documento_fattura'] ?? null),
        ];
    }

    protected function cleanString($value)
    {
        if (empty($value)) {
            return null;
        }
        
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Italian date formats
        $dateFormats = [
            'd/m/Y', 'd/m/Y', 'd-m-Y', 'd/m/Y',
            'd/m/y', 'd-m-y', 'Y-m-d'
        ];

        foreach ($dateFormats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Continue to next format
            }
        }

        // Try Excel serial date format
        if (is_numeric($value)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int)$value);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Continue to return null
            }
        }

        return null;
    }

    protected function parseDecimal($value)
    {
        if (empty($value)) {
            return null;
        }

        // Remove dots and commas for Italian format
        $cleanValue = str_replace(['.', ','], '', $value);
        
        if (is_numeric($cleanValue)) {
            return (float)($cleanValue / 100); // Convert from cents
        }

        return null;
    }

    protected function parseInteger($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return (int)$value;
        }

        return null;
    }

    protected function parseBoolean($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel TRUE/FALSE strings
        $value = strtoupper(trim($value));
        
        if ($value === 'TRUE' || $value === 'VERO') {
            return true;
        } elseif ($value === 'FALSE' || $value === 'FALSO') {
            return false;
        }

        return null;
    }

    public function getResults(): array
    {
        return $this->importResults;
    }
}
