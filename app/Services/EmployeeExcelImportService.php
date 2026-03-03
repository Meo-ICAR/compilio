<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeExcelImportService
{
    /**
     * Import employees from Excel file
     *
     * @param string $filePath Path to the Excel file
     * @param int $companyId Company ID to assign to employees
     * @return array Import results
     */
    public function importEmployees(string $filePath, int $companyId): array
    {
        try {
            $results = [
                'imported' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            // Load the Excel file
            $spreadsheet = Excel::toArray([], $filePath);

            // Get the 'responsabile interni' sheet
            $sheetData = $this->getSheetData($spreadsheet, 'responsabile interni');

            if (empty($sheetData)) {
                $results['errors'][] = 'Sheet "responsabile interni" not found or is empty';
                return $results;
            }

            // Skip header row and process data
            $dataRows = array_slice($sheetData, 1);

            DB::transaction(function () use ($dataRows, $companyId, &$results) {
                foreach ($dataRows as $index => $row) {
                    try {
                        $employeeData = $this->mapRowToEmployeeData($row, $companyId);

                        if (empty($employeeData['name'])) {
                            $results['skipped']++;
                            continue;
                        }

                        // Check if employee already exists
                        $existingEmployee = Employee::where('company_id', $companyId)
                            ->where('name', $employeeData['name'])
                            ->first();

                        if ($existingEmployee) {
                            // Update existing employee
                            $existingEmployee->update($employeeData);
                            $results['imported']++;
                        } else {
                            // Create new employee
                            Employee::create($employeeData);
                            $results['imported']++;
                        }
                    } catch (\Exception $e) {
                        $results['errors'][] = 'Row ' . ($index + 2) . ': ' . $e->getMessage();
                        $results['skipped']++;
                    }
                }
            });

            return $results;
        } catch (\Exception $e) {
            Log::error('Employee import failed: ' . $e->getMessage());
            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Get specific sheet data from spreadsheet
     *
     * @param array $spreadsheet
     * @param string $sheetName
     * @return array
     */
    private function getSheetData(array $spreadsheet, string $sheetName): array
    {
        foreach ($spreadsheet as $sheetIndex => $sheet) {
            // Try to match sheet by index (0-based) or name
            if ($sheetIndex === $sheetName || (is_string($sheetIndex) && strtolower($sheetIndex) === strtolower($sheetName))) {
                return $sheet;
            }
        }

        // If not found by name, try to get the first sheet
        return $spreadsheet[0] ?? [];
    }

    /**
     * Map Excel row to employee data
     *
     * @param array $row
     * @param int $companyId
     * @return array
     */
    private function mapRowToEmployeeData(array $row, int $companyId): array
    {
        // Adjust these indices based on your Excel file structure
        // This is a common structure - you may need to modify based on your actual Excel columns
        return [
            'company_id' => $companyId,
            'name' => $this->cleanString($row[0] ?? ''),  // Name
            'email' => $this->cleanString($row[1] ?? ''),  // Email
            'phone' => $this->cleanString($row[2] ?? ''),  // Phone
            'role_title' => $this->cleanString($row[3] ?? ''),  // Role/Position
            'department' => $this->cleanString($row[4] ?? ''),  // Department
            'employee_types' => $this->mapEmployeeType($row[5] ?? ''),  // Employee Type
            'supervisor_type' => $this->mapSupervisorType($row[6] ?? ''),  // Supervisor Type
            'is_structure' => $this->mapBoolean($row[7] ?? ''),  // Is Structure
            'is_ghost' => $this->mapBoolean($row[8] ?? ''),  // Is Ghost
            'hiring_date' => $this->mapDate($row[9] ?? ''),  // Hire Date
        ];
    }

    /**
     * Clean string value
     *
     * @param mixed $value
     * @return string
     */
    private function cleanString($value): string
    {
        return trim((string) $value);
    }

    /**
     * Map employee type to standard value
     *
     * @param string $value
     * @return string
     */
    private function mapEmployeeType(string $value): string
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'dipendente', 'employee' => 'dipendente',
            'collaboratore', 'collaborator' => 'collaboratore',
            'stagista', 'intern' => 'stagista',
            'consulente', 'consultant' => 'consulente',
            'amministratore', 'administrator' => 'amministratore',
            default => 'dipendente'
        };
    }

    /**
     * Map supervisor type to standard value
     *
     * @param string $value
     * @return string
     */
    private function mapSupervisorType(string $value): string
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'no', 'non', 'non supervisore' => 'no',
            'si', 'sì', 'supervisore' => 'si',
            'filiale', 'supervisore di filiale' => 'filiale',
            default => 'no'
        };
    }

    /**
     * Map string to boolean
     *
     * @param mixed $value
     * @return bool
     */
    private function mapBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['sì', 'si', 's', 'yes', 'y', 'true', '1', 'vero']);
    }

    /**
     * Map date string to date format
     *
     * @param mixed $value
     * @return string|null
     */
    private function mapDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            $date = \Carbon\Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Import from public/Registro Trattamenti.xlsx
     *
     * @param int $companyId
     * @return array
     */
    public function importFromPublicFile(int $companyId): array
    {
        $filePath = public_path('Registro Trattamenti.xlsx');

        if (!file_exists($filePath)) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => ["File not found: {$filePath}"]
            ];
        }

        return $this->importEmployees($filePath, $companyId);
    }
}
