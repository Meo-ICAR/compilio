<?php

namespace Database\Seeders;

use App\Models\ChecklistItem;
use App\Models\Company;
use App\Models\PracticeScope;
use App\Models\Process;
use App\Models\ProcessTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Otteniamo una company per i processi
        $company = Company::first();
        if (!$company) {
            $this->command->error('Nessuna company trovata. Impossibile eseguire il seeder.');
            return;
        }

        // Definizione dei flussi per tipologia di prodotto
        $workflowTemplates = [
            // CESSIONE DEL QUINTO (CQS/CQP)
            'CessioneCQS' => [
                ['name' => 'Raccolta Documenti e KYC', 'order' => 10],
                ['name' => 'Richiesta Certificato di Stipendio / Allegato A', 'order' => 20],
                ['name' => 'Verifica Merito e Fattibilità Assicurativa', 'order' => 30],
                ['name' => 'Caricamento Portale Banca / Finanziaria', 'order' => 40],
                ['name' => 'Emissione e Firma Contratti', 'order' => 50],
                ['name' => 'Notifica Atto al Terzo Ceduto', 'order' => 60],
                ['name' => 'Ottenimento Atto di Benestare', 'order' => 70],
                ['name' => 'Liquidazione e Post-Vendita', 'order' => 80],
            ],
            // MUTUI IPOTECARI
            'MUT_IPOTECARIO' => [
                ['name' => 'Analisi Preliminare e Consulenza', 'order' => 10],
                ['name' => 'Raccolta Documenti Reddituali e Immobile', 'order' => 20],
                ['name' => 'Istruttoria e Delibera Reddituale', 'order' => 30],
                ['name' => 'Prenotazione Perizia Immobile', 'order' => 40],
                ['name' => 'Relazione Notarile Preliminare (RNP)', 'order' => 50],
                ['name' => 'Delibera Definitiva e Stipula', 'order' => 60],
            ],
            // PRESTITI PERSONALI
            'CRED_PERS' => [
                ['name' => 'Intervista Cliente e Screening Creditizio', 'order' => 10],
                ['name' => 'Acquisizione Documentale e Privacy', 'order' => 20],
                ['name' => 'Invio Pratica e Esito Automatico', 'order' => 30],
                ['name' => 'Erogazione su Conto Corrente', 'order' => 40],
            ],
            // AZIENDALE / CHIROGRAFARIO
            'Aziendale' => [
                ['name' => 'Analisi Centrale Rischi e Bilanci', 'order' => 10],
                ['name' => 'Redazione Business Plan / Report Istruttorio', 'order' => 20],
                ['name' => 'Richiesta Garanzia MCC (Medio Credito Centrale)', 'order' => 30],
                ['name' => 'Delibera e Perfezionamento', 'order' => 40],
            ]
        ];

        // Inserimento task per processo OAM Richiesta fascicolo (process_id = 2)
        $oamTasks = [
            [
                'id' => 33,
                'process_id' => 2,
                'sort_order' => 10,
                'name' => 'Ricezione PEC e Protocollo Istanza',
                'code' => 'OAM-PEC',
                'slug' => 'ricezione-pec-e-protocollo-istanza',
            ],
            [
                'id' => 34,
                'process_id' => 2,
                'sort_order' => 20,
                'name' => 'Reperimento Fascicoli e Documenti',
                'code' => 'OAM_DOC',
                'slug' => 'reperimento-fascicoli-e-documenti',
            ],
            [
                'id' => 35,
                'process_id' => 2,
                'sort_order' => 30,
                'name' => 'Analisi Tecnica e Audit Pratiche',
                'code' => 'OAM-AUDIT',
                'slug' => 'analisi-tecnica-e-audit-pratiche',
            ],
            [
                'id' => 36,
                'process_id' => 2,
                'sort_order' => 40,
                'name' => 'Redazione Controdeduzioni OAM',
                'code' => 'OAM-ANALISI',
                'slug' => 'redazione-controdeduzioni-oam',
            ],
            [
                'id' => 37,
                'process_id' => 2,
                'sort_order' => 50,
                'name' => 'Invio Risposta e Archiviazione',
                'code' => 'OAM-RISPOSTA',
                'slug' => 'invio-risposta-e-archiviazione',
            ],
        ];

        foreach ($oamTasks as $task) {
            ProcessTask::updateOrCreate(
                ['id' => $task['id']],
                [
                    'process_id' => $task['process_id'],
                    'sort_order' => $task['sort_order'],
                    'name' => $task['name'],
                    //  'code' => $task['code'],
                    'slug' => $task['slug'],
                    'created_at' => '2026-03-21 08:15:58',
                    'updated_at' => '2026-03-21 08:15:58',
                ]
            );
        }
        $auditProcess = Process::updateOrCreate(
            ['slug' => 'audit-periodico-collaboratore'],
            [
                'company_id' => $company->id,
                'name' => 'Audit e Monitoraggio Collaboratori (OAM)',
                'periodicity' => 'annual',
                'is_active' => true,
            ]
        );

        $tasksAudit = [
            ['name' => 'Verifica regolarità iscrizione elenchi OAM e IVASS', 'slug' => 'audit-oam-check', 'order' => 10],
            ['name' => 'Controllo assolvimento obblighi formativi (Aggiornamento Professionale)', 'slug' => 'audit-training-verify', 'order' => 20],
            ['name' => 'Ispezione a campione sui fascicoli cartacei/digitali (Trasparenza)', 'slug' => 'audit-file-inspection', 'order' => 30],
            ['name' => 'Verifica segnalazioni reclami o anomalie AML sul collaboratore', 'slug' => 'audit-aml-complaints', 'order' => 40],
            ['name' => 'Relazione finale di audit e rinnovo mandato', 'slug' => 'audit-final-report', 'order' => 50],
        ];

        foreach ($tasksAudit as $t) {
            ProcessTask::updateOrCreate(
                ['slug' => $t['slug']],
                ['process_id' => $auditProcess->id, 'name' => $t['name'], 'sort_order' => $t['order']]
            );
        }

        // Database/Seeders/ChecklistCorrelationSeeder.php

        // 1. Task: Ispezione Fascicoli (Audit) -> Colleghiamo gli item della checklist 2 (OAM Audit)
        ChecklistItem::where('checklist_id', 2)
            ->update(['process_task_code' => 'audit-file-inspection']);

        // 2. Task: Istruttoria (CQS) -> Colleghiamo gli item della checklist 3 (CQS)
        ChecklistItem::where('checklist_id', 3)
            ->update(['process_task_code' => 'cqs-istruttoria']);

        // 3. Task: Verifica AML -> Colleghiamo la checklist 1 (AML)
        ChecklistItem::where('checklist_id', 1)
            ->update(['process_task_code' => 'aml-check-completeness']);

        $processes = [
            ['code' => 'PRC-AML', 'name' => 'Adeguata Verifica AML', 'description' => 'Identificazione e valutazione rischio'],
            ['code' => 'PRC-AUDIT', 'name' => 'Ispezione di Rete', 'description' => 'Verifica conformità territoriale'],
            ['code' => 'PRC-CQS', 'name' => 'Istruttoria CQS', 'description' => 'Gestione pratica Cessione del Quinto'],
        ];

        foreach ($processes as $p) {
            DB::table('processes')->updateOrInsert(['code' => $p['code']], $p);
        }

        $tasks = [
            // AML Tasks
            ['code' => 'aml-check-completeness', 'process_code' => 'PRC-AML', 'name' => 'Verifica Documentale'],
            ['code' => 'aml-high-risk-eval', 'process_code' => 'PRC-AML', 'name' => 'Valutazione Alto Rischio'],
            // Audit Tasks
            ['code' => 'audit-file-inspection', 'process_code' => 'PRC-AUDIT', 'name' => 'Ispezione Punti Vendita'],
            // CQS Tasks
            ['code' => 'cqs-istruttoria', 'process_code' => 'PRC-CQS', 'name' => 'Analisi Pratica'],
            ['code' => 'cqs-finalizzazione', 'process_code' => 'PRC-CQS', 'name' => 'Liquidazione e Benestare'],
        ];

        foreach ($tasks as $t) {
            DB::table('process_tasks')->updateOrInsert(['code' => $t['code']], $t);
        }

        $matrix = [
            // Task: aml-check-completeness
            ['task' => 'aml-check-completeness', 'func' => 'BUS-RETE-EXT', 'role' => 'R'],
            ['task' => 'aml-check-completeness', 'func' => 'BUS-BO', 'role' => 'A'],
            ['task' => 'aml-check-completeness', 'func' => 'CTRL-AML', 'role' => 'C'],
            // Task: aml-high-risk-eval
            ['task' => 'aml-high-risk-eval', 'func' => 'CTRL-AML', 'role' => 'R'],
            ['task' => 'aml-high-risk-eval', 'func' => 'GOV-CDA', 'role' => 'A'],
            // Task: audit-file-inspection
            ['task' => 'audit-file-inspection', 'func' => 'CTRL-AUDIT', 'role' => 'R'],
            ['task' => 'audit-file-inspection', 'func' => 'GOV-CDA', 'role' => 'A'],
            ['task' => 'audit-file-inspection', 'func' => 'CTRL-COMPL', 'role' => 'C'],
            // Task: cqs-istruttoria
            ['task' => 'cqs-istruttoria', 'func' => 'BUS-BO', 'role' => 'R'],
            ['task' => 'cqs-istruttoria', 'func' => 'BUS-DIRCOM', 'role' => 'A'],
        ];

        foreach ($matrix as $m) {
            $functionId = DB::table('business_functions')->where('code', $m['func'])->value('id');

            if ($functionId) {
                DB::table('process_raci')->updateOrInsert([
                    'process_task_code' => $m['task'],
                    'business_function_id' => $functionId,
                    'role' => $m['role']
                ]);
            }
        }
    }
}
