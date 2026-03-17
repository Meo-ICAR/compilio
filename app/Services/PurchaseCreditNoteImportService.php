<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Principal;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseCreditNoteImportService
{
    protected $companyId;
    protected $filename;

    protected $importResults = [
        'imported' => 0,
        'updated' => 0,
        'errors' => 0,
        'skipped' => 0,
        'details' => []
    ];

    public function __construct($companyId = null, $filename = null)
    {
        $this->companyId = $companyId;
        $this->filename = $filename;
    }

    /**
     * Import purchase credit notes from Excel file
     *
     * @param string $filePath Path to the file
     * @param string $companyId Company ID to assign to credit notes
     * @return array Import results
     */
    public function import(string $filePath, string $companyId = null): array
    {
        $this->companyId = $companyId ?: $this->companyId;

        // Extract filename from path if not provided
        if (!$this->filename) {
            $this->filename = basename($filePath);
        }

        $this->importResults = [
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
            'details' => []
        ];

        try {
            Log::info('Starting purchase credit note import', [
                'file_path' => $filePath,
                'filename' => $this->filename,
                'company_id' => $this->companyId
            ]);

            // Read Excel file
            $data = Excel::toArray([], $filePath);
            
            if (empty($data) || !isset($data[0])) {
                throw new \Exception('File is empty or invalid format');
            }

            $rows = $data[0];
            
            // Skip header row if present
            if ($this->isHeaderRow($rows[0])) {
                array_shift($rows);
            }

            Log::info('Processing rows', [
                'total_rows' => count($rows)
            ]);

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 1;
                
                try {
                    $this->processRow($row, $rowNumber);
                } catch (\Exception $e) {
                    $this->importResults['errors']++;
                    $this->importResults['details'][] = "Row {$rowNumber}: " . $e->getMessage();
                    
                    Log::error('Error processing row', [
                        'row_number' => $rowNumber,
                        'row_data' => $row,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Purchase credit note import completed', $this->importResults);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Purchase credit note import failed', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['errors']++;
            $this->importResults['details'][] = 'Import failed: ' . $e->getMessage();
        }

        return $this->importResults;
    }

    /**
     * Process a single row from the Excel file
     */
    protected function processRow(array $row, int $rowNumber): void
    {
        // Skip empty rows
        if ($this->isEmptyRow($row)) {
            $this->importResults['skipped']++;
            return;
        }

        // Extract data from row (adjust column indices based on your Excel structure)
        $creditNoteData = $this->extractCreditNoteData($row);

        // Validate required fields
        if (empty($creditNoteData['number']) || empty($creditNoteData['date'])) {
            throw new \Exception('Missing required fields: number or date');
        }

        // Check if credit note already exists
        $existingCreditNote = PurchaseInvoice::where('company_id', $this->companyId)
            ->where('number', $creditNoteData['number'])
            ->where('date', $creditNoteData['date'])
            ->first();

        if ($existingCreditNote) {
            // Update existing record
            $existingCreditNote->update($creditNoteData);
            $this->importResults['updated']++;
            
            Log::info('Updated existing purchase credit note', [
                'row_number' => $rowNumber,
                'credit_note_id' => $existingCreditNote->id,
                'number' => $creditNoteData['number']
            ]);
        } else {
            // Create new credit note
            $creditNote = PurchaseInvoice::create([
                'company_id' => $this->companyId,
                'type' => 'credit_note', // Assuming there's a type field
                ...$creditNoteData
            ]);
            
            $this->importResults['imported']++;
            
            Log::info('Created new purchase credit note', [
                'row_number' => $rowNumber,
                'credit_note_id' => $creditNote->id,
                'number' => $creditNoteData['number']
            ]);
        }
    }

    /**
     * Extract credit note data from Excel row
     * Adjust this method based on your Excel column structure
     */
    protected function extractCreditNoteData(array $row): array
    {
        // This is a template - adjust column indices based on your Excel structure
        return [
            'number' => $this->cleanString($row[0] ?? ''),
            'date' => $this->parseDate($row[1] ?? null),
            'supplier' => $this->cleanString($row[2] ?? ''),
            'description' => $this->cleanString($row[3] ?? ''),
            'amount' => $this->parseAmount($row[4] ?? 0),
            'amount_including_vat' => $this->parseAmount($row[5] ?? 0),
            'vat_rate' => $this->parseAmount($row[6] ?? 0),
            'residual_amount' => $this->parseAmount($row[7] ?? 0),
            'due_date' => $this->parseDate($row[8] ?? null),
            'payment_date' => $this->parseDate($row[9] ?? null),
            'notes' => $this->cleanString($row[10] ?? ''),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Check if the first row is a header row
     */
    protected function isHeaderRow(array $row): bool
    {
        // Check if first row contains typical header values
        $headerIndicators = ['numero', 'data', 'fornitore', 'importo', 'description'];
        $firstRow = array_map('strtolower', array_map('trim', $row));
        
        foreach ($headerIndicators as $indicator) {
            if (in_array($indicator, $firstRow)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if row is empty
     */
    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (!empty($cell) && trim($cell) !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Clean string value
     */
    protected function cleanString($value): string
    {
        if (is_null($value)) {
            return '';
        }
        
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    /**
     * Parse date from Excel format
     */
    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel serial date format
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('Y-m-d');
        }

        // Handle string date format
        try {
            $date = new \DateTime($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Could not parse date', ['value' => $value, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parse amount from Excel
     */
    protected function parseAmount($value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Remove currency symbols and formatting
        $cleaned = preg_replace('/[^0-9.,-]/', '', (string) $value);
        $cleaned = str_replace(',', '.', $cleaned);
        
        return (float) $cleaned;
    }

    /**
     * Get import results
     */
    public function getResults(): array
    {
        return $this->importResults;
    }
}
