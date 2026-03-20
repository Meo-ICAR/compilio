<?php

namespace App\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\Importers\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImporter extends Importer implements ToModel, WithHeadingRow, WithValidation
{
    private int $companyId;
    private int $importedCount = 0;
    private int $updatedCount = 0;
    private array $errors = [];

    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    public function model(array $row): Employee
    {
        try {
            Log::info('Processing employee row', [
                'row_data' => $row,
                'company_id' => $this->companyId
            ]);

            // Map Excel columns to employee fields
            $employeeData = [
                'company_id' => $this->companyId,
                'name' => $row['cognome'] ?? '',
                'first_name' => $row['nome'] ?? '',
                'email' => $row['email'] ?? '',
                'phone' => $row['telefono'] ?? '',
                'role' => $row['ruolo'] ?? '',
                'department' => $row['dipartimento'] ?? '',
                'hire_date' => $this->parseDate($row['data_assunzione'] ?? null),
                'employee_types' => $row['tipologia'] ?? 'dipendente',
                'supervisor_type' => $row['tipo_supervisore'] ?? 'no',
                'cf' => $row['codice_fiscale'] ?? '',
            ];

            // Check if employee already exists
            $existingEmployee = Employee::where('company_id', $this->companyId)
                ->where('email', $employeeData['email'])
                ->first();

            if ($existingEmployee) {
                $existingEmployee->update($employeeData);
                $this->updatedCount++;
                Log::info('Employee updated', [
                    'employee_id' => $existingEmployee->id,
                    'name' => $existingEmployee->name,
                    'company_id' => $this->companyId
                ]);
                return $existingEmployee;
            }

            // Create new employee
            $employee = Employee::create($employeeData);
            $this->importedCount++;

            Log::info('Employee created', [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'company_id' => $this->companyId
            ]);

            return $employee;
        } catch (\Exception $e) {
            Log::error('Error creating employee', [
                'error' => $e->getMessage(),
                'row_data' => $row,
                'company_id' => $this->companyId
            ]);

            $this->errors[] = $e->getMessage();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cognome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'ruolo' => 'nullable|string|max:255',
            'dipartimento' => 'nullable|string|max:255',
            'data_assunzione' => 'nullable|date',
            'tipologia' => 'nullable|string|in:dipendente,collaboratore,stagista,consulente,amministratore',
            'tipo_supervisore' => 'nullable|string|in:no,si,filiale',
            'codice_fiscale' => 'nullable|string|max:16',
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

    // Required methods for Filament Importer
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getErrorsCount(): int
    {
        return count($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function getLabel(): string
    {
        return 'Dipendenti';
    }

    public static function getIcon(): string
    {
        return 'heroicon-o-users';
    }
}
