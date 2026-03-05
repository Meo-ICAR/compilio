<?php

namespace Database\Seeders;

use App\Models\BusinessFunction;
use Illuminate\Database\Seeder;

class BusinessFunctionSeeder extends Seeder
{
    public function run()
    {
        $funzioniAziendali = [
            [
                'code' => 'GOV-CDA',
                'macro_area' => 'Governance',
                'name' => 'Consiglio di Amministrazione / Direzione',
                'type' => 'Strategica',
                'description' => 'Definisce strategie, approva procedure organizzative, politiche di rischio e assicura l’adeguatezza dell’assetto organizzativo.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-DIRCOM',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Direzione Commerciale',
                'type' => 'Operativa',
                'description' => 'Sviluppo accordi con Banche/Finanziarie, monitoraggio volumi e coordinamento Area Manager.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-RETE-GEST',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Gestione Rete e Collaboratori',
                'type' => 'Operativa',
                'description' => 'Selezione, iscrizione elenchi OAM e monitoraggio dell’operato dei collaboratori esterni.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-RETE-EXT',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Gestione Rete e Collaboratori',  // Nota: Nome enum uguale ma code diverso
                'type' => 'Operativa',
                'description' => 'Agenti e collaboratori sul territorio: vendita, relazione cliente e raccolta documentale primaria.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-BO',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Back Office / Istruttoria Pratiche',
                'type' => 'Operativa',
                'description' => 'Istruttoria, controlli di I livello, caricamento portali bancari e gestione benestari CQS.',
                'outsourcable_status' => 'partial',
            ],
            [
                'code' => 'SUP-AMM',
                'macro_area' => 'Supporto',
                'name' => 'Amministrazione e Contabilità',
                'type' => 'Supporto',
                'description' => 'Contabilità, fatturazione provvigioni attive/passive e gestione flussi finanziari.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'SUP-IT',
                'macro_area' => 'Supporto',
                'name' => 'IT e Sicurezza Dati',
                'type' => 'Supporto',
                'description' => 'Gestione CRM, sicurezza informatica e continuità operativa.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'SUP-RECLAMI',
                'macro_area' => 'Supporto',
                'name' => 'Gestione Reclami e Controversie',
                'type' => 'Supporto',
                'description' => 'Analisi reclami, gestione ricorsi ABF e reporting per la Direzione.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'CTRL-COMPL',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Compliance (Conformità)',
                'type' => 'Controllo',
                'description' => 'Prevenzione del rischio di non conformità normativa (Trasparenza, OAM, Privacy).',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'CTRL-AML',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Antiriciclaggio (AML)',
                'type' => 'Controllo',
                'description' => 'Profilatura rischio, tenuta AUI, analisi operazioni sospette e segnalazioni SOS.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'CTRL-AUDIT',
                'macro_area' => 'Controlli (III Livello)',
                'name' => 'Internal Audit (Revisione Interna)',
                'type' => 'Controllo',
                'description' => 'Ispezioni indipendenti e test a campione su tutto l’impianto organizzativo.',
                'outsourcable_status' => 'yes',
            ],
        ];

        foreach ($funzioniAziendali as $item) {
            BusinessFunction::updateOrCreate(
                ['code' => $item['code']],  // Usa il code come chiave per evitare duplicati
                $item
            );
        }
    }
}
