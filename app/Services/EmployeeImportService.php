<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Rui;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeImportService implements ToModel, WithHeadingRow, WithValidation
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

    public function __construct($companyId = null)
    {
        $this->companyId = $companyId;
    }

    public function import($filePath, $companyId = null)
    {
        if ($companyId) {
            $this->companyId = $companyId;
        }

        // Extract filename from path if not provided
        $this->filename = basename($filePath);

        $this->importResults = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => []
        ];

        try {
            Log::info('Starting employee import', [
                'file_path' => $filePath,
                'company_id' => $this->companyId,
                'file_exists' => file_exists($filePath),
                'file_size' => file_exists($filePath) ? filesize($filePath) : 'N/A'
            ]);

            // Check if file exists and is readable
            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            if (!is_readable($filePath)) {
                throw new \Exception("File is not readable: {$filePath}");
            }

            Log::info('File validated, starting Excel import');

            // Use collection import to handle multi-sheet files
            $collections = Excel::toCollection(null, $filePath);

            Log::info('Excel sheets loaded', [
                'sheets_count' => $collections->count(),
                'sheet_names' => $collections->keys()->toArray()
            ]);

            foreach ($collections as $sheetName => $sheetData) {
                Log::info('Processing sheet', [
                    'sheet_name' => $sheetName,
                    'rows_count' => $sheetData->count()
                ]);

                // Skip empty sheets
                if ($sheetData->isEmpty()) {
                    Log::info('Skipping empty sheet', ['sheet_name' => $sheetName]);
                    continue;
                }

                // Process each row in the sheet
                foreach ($sheetData as $index => $row) {
                    try {
                        // Skip header row if it exists
                        if ($index === 0 && $this->isHeaderRow($row)) {
                            Log::info('Skipping header row', ['sheet_name' => $sheetName, 'row_index' => $index]);
                            continue;
                        }

                        // Convert collection to array for model method
                        $rowArray = $row->toArray();

                        // Skip empty rows
                        if ($this->isEmptyRow($rowArray)) {
                            Log::info('Skipping empty row', ['sheet_name' => $sheetName, 'row_index' => $index]);
                            $this->importResults['skipped']++;
                            continue;
                        }

                        $this->model($rowArray);
                    } catch (\Exception $e) {
                        Log::error('Error processing row', [
                            'sheet_name' => $sheetName,
                            'row_index' => $index,
                            'error' => $e->getMessage(),
                            'row_data' => $row
                        ]);

                        $this->importResults['errors']++;
                        $this->importResults['details'][] = "Sheet '{$sheetName}' Row " . ($index + 1) . ': ' . $e->getMessage();
                    }
                }
            }

            Log::info('Employee import completed', [
                'imported' => $this->importResults['imported'],
                'updated' => $this->importResults['updated'],
                'skipped' => $this->importResults['skipped'],
                'errors' => $this->importResults['errors'],
                'details' => $this->importResults['details']
            ]);

            return $this->importResults;
        } catch (\Exception $e) {
            Log::error('Employee import failed', [
                'error' => $e->getMessage(),
                'file_path' => $filePath,
                'company_id' => $this->companyId,
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['errors']++;
            $this->importResults['details'][] = 'Import failed: ' . $e->getMessage();

            return $this->importResults;
        }
    }

    private function isHeaderRow($row): bool
    {
        // Check if row contains common header keywords
        $headerKeywords = ['nome', 'cognome', 'email', 'pec', 'telefono', 'ruolo', 'dipartimento'];

        foreach ($headerKeywords as $keyword) {
            foreach ($row as $cell) {
                if (is_string($cell) && stripos($cell, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isEmptyRow(array $row): bool
    {
        $nonEmptyCount = 0;
        foreach ($row as $value) {
            if (!empty($value) && $value !== '' && $value !== null) {
                $nonEmptyCount++;
            }
        }

        return $nonEmptyCount === 0;
    }

    public function model(array $row): ?Employee
    {
        try {
            Log::info('Processing employee row', [
                'row_data' => $row,
                'company_id' => $this->companyId,
                'row_keys' => array_keys($row)
            ]);

            // Check if required fields exist (using numeric indices)
            if (empty(trim($row[0] ?? '')) && empty(trim($row[1] ?? ''))) {
                Log::warning('Skipping row - no name data', ['row' => $row]);
                $this->importResults['skipped']++;
                $this->importResults['details'][] = 'Skipped row: No name data found';
                return null;
            }

            // Map Excel columns to employee fields (using numeric indices based on actual file structure)
            $employeeData = [
                'company_id' => $this->companyId,
                'name' => trim($row[0] ?? ''),  // COGNOME
                'email' => trim($row[6] ?? ''),  // INDIRIZZO EMAIL AZIENDALE
                'pec' => '',  // PEC (not in this file)
                'phone' => trim($row[4] ?? '') ?: trim($row[5] ?? ''),  // TELEFONO FISSO or CELLULARE
                'numero_iscrizione_rui' => $row[2] ?? null,  // DATA ISCR. OAM (actually registration number)
                'oam_at' => null,  // Not in file
                'oam_name' => trim(($row[0] ?? '') . ' ' . ($row[1] ?? '')),  // Full name
            ];

            Log::info('Mapped employee data', [
                'employee_data' => $employeeData,
                'email' => $employeeData['email']
            ]);

            // Check if employee already exists
            $query = Employee::where('company_id', $this->companyId);

            if (!empty($employeeData['email'])) {
                $query->where('email', $employeeData['email']);
                Log::info('Checking existing employee by email', ['email' => $employeeData['email']]);
            } else {
                // If no email, check by name
                $query
                    ->where('name', $employeeData['name'])
                    ->where('first_name', $employeeData['first_name']);
                Log::info('Checking existing employee by name', [
                    'name' => $employeeData['name'],
                    'first_name' => $employeeData['first_name']
                ]);
            }

            $existingEmployee = $query->first();

            if ($existingEmployee) {
                Log::info('Found existing employee, updating', [
                    'employee_id' => $existingEmployee->id,
                    'existing_name' => $existingEmployee->name
                ]);

                $existingEmployee->update($employeeData);
                $this->importResults['updated']++;

                Log::info('Employee updated', [
                    'employee_id' => $existingEmployee->id,
                    'name' => $existingEmployee->name,
                    'company_id' => $this->companyId
                ]);

                return $existingEmployee;
            }

            Log::info('Creating new employee', ['employee_data' => $employeeData]);

            // Create new employee
            $employee = Employee::create($employeeData);
            $this->importResults['imported']++;

            Log::info('Employee created', [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'first_name' => $employee->first_name,
                'email' => $employee->email,
                'company_id' => $this->companyId
            ]);

            return $employee;
        } catch (\Exception $e) {
            Log::error('Error creating employee', [
                'error' => $e->getMessage(),
                'row_data' => $row,
                'company_id' => $this->companyId,
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['errors']++;
            $this->importResults['details'][] = 'Row error: ' . $e->getMessage();

            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'pec' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'ruolo' => 'nullable|string|max:255',
            'dipartimento' => 'nullable|string|max:255',
            'data_assunzione' => 'nullable|date',
            'tipologia' => 'nullable|string|in:dipendente,collaboratore,stagista,consulente,amministratore',
            'tipo_supervisore' => 'nullable|string|in:no,si,filiale',
            'codice_fiscale' => 'nullable|string|max:16',
            'ragione_sociale' => 'nullable|string|max:255',
        ];
    }

    private function parseDate($dateString): ?\Carbon\Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
        } catch (\Exception $e) {
            Log::warning('Invalid date format', ['date' => $dateString, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function matchEmployeesByRui($companyId = null): array
    {
        $companyId = $companyId ?? $this->companyId;
        $matchedCount = 0;

        try {
            Log::info('Starting employee RUI matching', ['company_id' => $companyId]);

            // Get all employees for this company that don't have RUI registration
            $employees = Employee::where('company_id', $companyId)
                ->whereNull('numero_iscrizione_rui')
                ->get();

            foreach ($employees as $employee) {
                // Try to find matching RUI record by name
                $rui = Rui::where('cognome_nome', 'like', '%' . $employee->name . '%')
                    ->first();

                if ($rui && !$employee->numero_iscrizione_rui) {
                    // Update employee with RUI data
                    $employee->update([
                        'numero_iscrizione_rui' => $rui->numero_iscrizione_rui,
                        'oam_at' => $rui->data_iscrizione,
                        'oam_name' => $rui->cognome_nome
                    ]);

                    $matchedCount++;
                    Log::info('Employee matched with RUI', [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'rui_number' => $rui->numero_iscrizione_rui,
                        'rui_name' => $rui->cognome_nome
                    ]);
                }
            }

            $results = [
                'matched' => $matchedCount,
                'total' => $employees->count(),
            ];

            Log::info('Employee RUI matching completed', $results);

            return $results;
        } catch (\Exception $e) {
            Log::error('Employee RUI matching failed', [
                'error' => $e->getMessage(),
                'company_id' => $companyId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'matched' => 0,
                'total' => 0,
            ];
        }
    }
}
