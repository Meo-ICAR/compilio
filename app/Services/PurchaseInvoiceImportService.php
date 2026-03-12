<?php

namespace App\Services;

use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseInvoiceImportService
{
    protected $companyId;

    protected $importResults = [
        'imported' => 0,
        'updated' => 0,
        'errors' => 0,
        'skipped' => 0,
        'details' => []
    ];

    public function __construct($companyId = null)
    {
        $this->companyId = $companyId;
    }

    /**
     * Import purchase invoices from CSV/Excel file
     *
     * @param string $filePath Path to the file
     * @param string $companyId Company ID to assign to invoices
     * @return array Import results
     */
    public function import(string $filePath, string $companyId = null): array
    {
        $this->companyId = $companyId ?: $this->companyId;
        $this->importResults = [
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
            'details' => []
        ];

        try {
            // Use direct CSV parsing for better control
            $this->importFromCSV($filePath);

            Log::info('Purchase invoices import completed', [
                'file' => $filePath,
                'company_id' => $this->companyId,
                'results' => $this->importResults
            ]);

            return $this->importResults;
        } catch (\Exception $e) {
            Log::error('Purchase invoices import failed', [
                'file' => $filePath,
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);

            $this->importResults['success'] = false;
            $this->importResults['message'] = $e->getMessage();

            return $this->importResults;
        }
    }

    protected function importFromCSV(string $filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open file: $filePath");
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
            $cleanHeader = str_replace(['.', ' ', '-', '(', ')'], ['_', '_', '_', '_', '_'], $cleanHeader);
            $cleanHeader = strtolower($cleanHeader);
            $cleanHeaders[] = $cleanHeader;
        }

        Log::info('CSV Headers', ['original' => $headers, 'cleaned' => $cleanHeaders]);

        $rowNumber = 2;  // Start from 2 since we already read header
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $this->processRow($row, $cleanHeaders, $rowNumber);
                $rowNumber++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        fclose($handle);
    }

    protected function processRow(array $row, array $headers, int $rowNumber)
    {
        try {
            // Use direct index mapping instead of array_combine for reliability
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }

            // Debug first few rows
            static $debugCount = 0;
            if ($debugCount < 3) {
                Log::info("Debug row $debugCount (row $rowNumber)", [
                    'headers' => $headers,
                    'row' => $row,
                    'rowData' => $rowData,
                    'nr_value' => $rowData['nr_'] ?? 'NOT_FOUND',
                    'fornitore_value' => $rowData['fornitore'] ?? 'NOT_FOUND',
                    'check_nr_empty' => empty($rowData['nr_']),
                    'check_fornitore_empty' => empty($rowData['fornitore'])
                ]);
                $debugCount++;
            }

            // Skip empty rows
            if (empty($rowData['nr_']) || empty($rowData['fornitore'])) {
                Log::info("Skipping row $rowNumber", [
                    'nr_' => $rowData['nr_'],
                    'fornitore' => $rowData['fornitore']
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
                $existingInvoice->update($invoiceData);
                $this->importResults['updated']++;
                $this->importResults['details'][] = "Updated invoice: {$invoiceData['number']} (row $rowNumber)";
            } else {
                $invoice = PurchaseInvoice::create($invoiceData);
                $this->importResults['imported']++;
                $this->importResults['details'][] = "Imported invoice: {$invoiceData['number']} (row $rowNumber)";
            }
        } catch (\Exception $e) {
            $this->importResults['errors']++;
            $errorDetails = "Error processing row $rowNumber: " . $e->getMessage();
            $this->importResults['details'][] = $errorDetails;
            Log::error('Purchase invoice import error', [
                'row_number' => $rowNumber,
                'row' => $row,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function mapRowToInvoiceData(array $row): array
    {
        return [
            'number' => $this->cleanString($row['nr_'] ?? $row['nr'] ?? null),
            'supplier_invoice_number' => $this->cleanString($row['nr__fatt__fornitore'] ?? null),
            'supplier_number' => $this->cleanString($row['nr__fornitore'] ?? null),
            'supplier' => $this->cleanString($row['fornitore'] ?? null),
            'currency_code' => $this->cleanString($row['cod__valuta'] ?? null),
            'amount' => $this->parseDecimal($row['importo'] ?? null),
            'amount_including_vat' => $this->parseDecimal($row['importo_iva_inclusa'] ?? null),
            'pay_to_cap' => $this->cleanString($row['pagare_a___cap'] ?? null),
            'pay_to_country_code' => $this->cleanString($row['pagare_a___cod__paese'] ?? null),
            'registration_date' => $this->parseDate($row['data_di_registrazione'] ?? null),
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
        if ($value === null || $value === '') {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    protected function parseDecimal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remove dots and replace comma with dot for Italian decimal format
        $cleaned = str_replace('.', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        if (is_numeric($cleaned)) {
            return (float) $cleaned;
        }

        return null;
    }

    protected function parseInteger($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $value);

        if (is_numeric($cleaned)) {
            return (int) $cleaned;
        }

        return 0;
    }

    protected function parseDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            // Try Italian date format d/m/Y first
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value);
            }

            // Try other common formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'Y-m-d H:i:s'];

            foreach ($formats as $format) {
                try {
                    return \Carbon\Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }

            // If no format works, try Carbon's flexible parsing
            return new \Carbon\Carbon($value);
        } catch (\Exception $e) {
            Log::warning('Failed to parse date: ' . $value);
            return null;
        }
    }

    protected function parseBoolean($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        // Italian boolean values
        $trueValues = ['VERO', 'TRUE', '1', 'SI', 'SÌ', 'YES'];
        $falseValues = ['FALSO', 'FALSE', '0', 'NO'];

        $upperValue = strtoupper(trim($value));

        if (in_array($upperValue, $trueValues)) {
            return true;
        }

        if (in_array($upperValue, $falseValues)) {
            return false;
        }

        return false;
    }
}
