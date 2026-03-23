<?php

namespace Database\Seeders;

use App\Models\BusinessFunction;
use App\Models\Company;
use App\Models\Process;
use App\Models\ProcessTask;
use App\Models\RaciAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OamProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        // Otteniamo una company per i processi
        $company = Company::first();
        if (!$company) {
            $this->command->error('Nessuna company trovata. Impossibile eseguire il seeder.');
            return;
        }

        DB::table('processes')->updateOrInsert(
            ['id' => 2],
            [
                'company_id' => $company->id,
                'name' => 'OAM Richiesta fascicolo',
                'slug' => Str::slug('OAM Richiesta fascicolo'),
                'groupcode' => 'OAM-INQUIRY',
                'periodicity' => 'once',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        $auditProcess = Process::updateOrCreate(
            ['slug' => 'audit-periodico-collaboratore'],
            [
                'company_id' => $company->id,
                'name' => 'Audit e Monitoraggio Collaboratori (OAM)',
                'periodicity' => 'annual',
                'is_active' => true,
            ]
        );
        $this->command->info('Process "OAM Richiesta fascicolo" created successfully.');

        $matrix = [
            // --- 1. PROCESSO: SEGNALAZIONE SEMESTRALE OAM ---
            'oam-data-extraction' => [
                'SUP-AMM' => 'R',
                'BUS-DIRCOM' => 'A',
                'SUP-IT' => 'C',
                'CTRL-COMPL' => 'I',
            ],
            'oam-data-validation' => [
                'CTRL-COMPL' => 'R',
                'CTRL-COMPL' => 'A',
                'SUP-AMM' => 'C',
                'GOV-CDA' => 'I',
            ],
            'oam-portal-upload' => [
                'BUS-BO' => 'R',
                'CTRL-COMPL' => 'A',
                'SUP-IT' => 'C',
            ],
            'oam-final-submission' => [
                'GOV-CDA' => 'R',
                'GOV-CDA' => 'A',
                'CTRL-COMPL' => 'C',
                'BUS-DIRCOM' => 'I',
            ],
            // --- 2. PROCESSO: AUDIT PERIODICO COLLABORATORE ---
            'audit-oam-check' => [
                'BUS-RETE-GEST' => 'R',
                'CTRL-COMPL' => 'A',
                'GOV-CDA' => 'I',
            ],
            'audit-training-verify' => [
                'BUS-RETE-GEST' => 'R',
                'BUS-DIRCOM' => 'A',
                'CTRL-COMPL' => 'C',
            ],
            'audit-file-inspection' => [
                'CTRL-AUDIT' => 'R',
                'CTRL-AUDIT' => 'A',
                'BUS-BO' => 'C',
                'BUS-RETE-GEST' => 'I',
            ],
            'audit-aml-complaints' => [
                'CTRL-AML' => 'R',
                'CTRL-AML' => 'A',
                'SUP-RECLAMI' => 'C',
                'GOV-CDA' => 'I',
            ],
            'audit-final-report' => [
                'CTRL-COMPL' => 'R',
                'GOV-CDA' => 'A',
                'CTRL-AUDIT' => 'C',
                'BUS-DIRCOM' => 'I',
            ],
        ];

        foreach ($matrix as $taskSlug => $assignments) {
            // Recupera il Task tramite slug
            $task = ProcessTask::where('slug', $taskSlug)->first();

            if (!$task) {
                $this->command->warn("Task non trovato per lo slug: {$taskSlug}");
                continue;
            }

            foreach ($assignments as $functionCode => $role) {
                // Recupera la Funzione tramite il codice univoco (es. GOV-CDA)
                $function = BusinessFunction::where('code', $functionCode)->first();

                if ($function) {
                    RaciAssignment::updateOrCreate(
                        [
                            'process_task_id' => $task->id,
                            'business_function_id' => $function->id,
                        ],
                        [
                            'role' => $role,
                            'updated_at' => now(),
                        ]
                    );
                } else {
                    $this->command->error("Funzione Business non trovata: {$functionCode}");
                }
            }
        }

        $this->command->info('Matrice RACI popolata con successo!');
    }
}
