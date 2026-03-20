<?php

namespace App\Imports;

use App\Models\Agent;
use Filament\Actions\Imports\Importers\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AgentsImporter extends Importer implements ToModel, WithHeadingRow, WithValidation
{
    private int $companyId;
    private int $importedCount = 0;
    private int $updatedCount = 0;
    private array $errors = [];

    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    public function model(array $row): Agent
    {
        try {
            Log::info('Processing agent row', [
                'row_data' => $row,
                'company_id' => $this->companyId
            ]);

            // Map Excel columns to agent fields
            $agentData = [
                'company_id' => $this->companyId,
                'name' => $row['cognome'] ?? '',
                'first_name' => $row['nome'] ?? '',
                'email' => $row['email'] ?? '',
                'phone' => $row['telefono'] ?? '',
                'role' => $row['ruolo'] ?? '',
                'department' => $row['dipartimento'] ?? '',
                'hire_date' => $this->parseDate($row['data_assunzione'] ?? null),
                'agent_types' => $row['tipologia'] ?? 'dipendente',
                'supervisor_type' => $row['tipo_supervisore'] ?? 'no',
                'cf' => $row['codice_fiscale'] ?? '',
            ];

            // Check if agent already exists
            $existingAgent = Agent::where('company_id', $this->companyId)
                ->where('email', $agentData['email'])
                ->first();

            if ($existingAgent) {
                $existingAgent->update($agentData);
                $this->updatedCount++;
                Log::info('Agent updated', [
                    'agent_id' => $existingAgent->id,
                    'name' => $existingAgent->name,
                    'company_id' => $this->companyId
                ]);
                return $existingAgent;
            }

            // Create new agent
            $agent = Agent::create($agentData);
            $this->importedCount++;

            Log::info('Agent created', [
                'agent_id' => $agent->id,
                'name' => $agent->name,
                'company_id' => $this->companyId
            ]);

            return $agent;
        } catch (\Exception $e) {
            Log::error('Error creating agent', [
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
