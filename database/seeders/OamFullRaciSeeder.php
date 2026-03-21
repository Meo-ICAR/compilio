<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OamFullRaciSeeder extends Seeder
{
    public function run(): void
    {
        // Definiamo le 5 fasi del processo
        $steps = [
            'OAM-01' => ['name' => 'Ricezione PEC e Protocollo Istanza', 'groupcode' => 'OAM-01', 'code' => 'RICEZIONE-PEC', 'r' => 'BUS-BO', 'a' => 'GOV-CDA', 'c' => 'CTRL-COMPL', 'i' => 'SUP-LEG-AMM'],
            'OAM-02' => ['name' => 'Reperimento Fascicoli e Documenti', 'groupcode' => 'OAM-02', 'code' => 'REPERIMENTO-FASCICOLI', 'r' => 'BUS-BO', 'a' => 'CTRL-COMPL', 'c' => 'BUS-RETE-EXT', 'i' => 'GOV-CDA'],
            'OAM-03' => ['name' => 'Analisi Tecnica e Audit Pratiche', 'groupcode' => 'OAM-03', 'code' => 'ANALISI-TECNICA', 'r' => 'CTRL-COMPL', 'a' => 'GOV-CDA', 'c' => 'BUS-DIRCOM', 'i' => 'SUP-RECLAMI'],
            'OAM-04' => ['name' => 'Redazione Controdeduzioni OAM', 'groupcode' => 'OAM-04', 'code' => 'CONTRODEDUZIONI', 'r' => 'CTRL-COMPL', 'a' => 'GOV-CDA', 'c' => 'SUP-LEG-AMM', 'i' => 'BUS-BO'],
            'OAM-05' => ['name' => 'Invio Risposta e Archiviazione', 'groupcode' => 'OAM-05', 'code' => 'INVIO-RISPOSTA', 'r' => 'BUS-BO', 'a' => 'GOV-CDA', 'c' => 'CTRL-COMPL', 'i' => 'CTRL-AUDIT'],
        ];

        // Recuperiamo le funzioni per mappare i codici agli ID
        $functions = DB::table('business_functions')->get()->keyBy('code');

        foreach ($steps as $code => $data) {
            // 1. Inserimento del Task
            $taskId = DB::table('process_tasks')->insertGetId([
                'taskable_id' => 99,  // ID convenzionale per Compliance/OAM (PracticeScope)
                'taskable_type' => 'App\Models\PracticeScope',
                'name' => $data['name'],
                'groupcode' => $data['groupcode'],
                'code' => $data['code'],
                'slug' => Str::slug($data['name']),
                'sort_order' => (int) filter_var($code, FILTER_SANITIZE_NUMBER_INT) * 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Inserimento delle assegnazioni RACI (R, A, C, I)
            foreach (['r', 'a', 'c', 'i'] as $roleLetter) {
                $funcCode = $data[$roleLetter];
                $roleUpper = strtoupper($roleLetter);

                if (isset($functions[$funcCode])) {
                    DB::table('raci_assignments')->insert([
                        'process_task_id' => $taskId,
                        'business_function_id' => $functions[$funcCode]->id,
                        'role' => $roleUpper,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
