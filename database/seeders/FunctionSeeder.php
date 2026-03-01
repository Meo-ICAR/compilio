<?php

namespace Database\Seeders;

use App\Models\CompanyFunction;
use Illuminate\Database\Seeder;

class FunctionSeeder extends Seeder
{
    public function run()
    {
        $funzioniAziendali = [
            [
                'code' => 'GOV-CDA',
                'macro_area' => 'Governance',
                'name' => 'Consiglio di Amministrazione / Direzione',
                'type' => 'Strategica',
                'description' => 'Definisce le strategie aziendali, approva le procedure organizzative, le politiche commerciali e il sistema di gestione dei rischi.',
                'outsourcable_status' => 'no'
            ],
            [
                'code' => 'BUS-DIRCOM',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Direzione Commerciale',
                'type' => 'Operativa',
                'description' => 'Definisce i budget, sviluppa le convenzioni con le banche/finanziarie e coordina gli Area Manager.',
                'outsourcable_status' => 'no'
            ],
            [
                'code' => 'BUS-RETE',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Gestione Rete e Collaboratori',
                'type' => 'Operativa',
                'description' => 'Selezione, inserimento e monitoraggio commerciale della rete (dipendenti e collaboratori ex art. 128-novies TUB).',
                'outsourcable_status' => 'no'
            ],
            [
                'code' => 'BUS-BO',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Back Office / Istruttoria Pratiche',
                'type' => 'Operativa',
                'description' => 'Caricamento pratiche, raccolta documenti del cliente, controlli di I livello e invio alla banca.',
                'outsourcable_status' => 'partial'
            ],
            [
                'code' => 'SUP-AMM',
                'macro_area' => 'Supporto',
                'name' => 'Amministrazione e Contabilità',
                'type' => 'Supporto',
                'description' => 'Fatturazione attiva e passiva, pagamento provvigioni ai collaboratori, bilancio, adempimenti fiscali.',
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'SUP-IT',
                'macro_area' => 'Supporto',
                'name' => 'IT e Sicurezza Dati',
                'type' => 'Supporto',
                'description' => "Gestione dell'infrastruttura tecnologica, server, CRM/Gestionale pratiche, cybersecurity e presidio Data Breach.",
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'SUP-MKTG',
                'macro_area' => 'Supporto',
                'name' => 'Marketing e Comunicazione',
                'type' => 'Supporto',
                'description' => 'Creazione di campagne pubblicitarie, gestione sito web e social media.',
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'SUP-RECLAMI',
                'macro_area' => 'Supporto',
                'name' => 'Gestione Reclami e Controversie',
                'type' => 'Supporto',
                'description' => 'Ricezione, analisi e riscontro formale ai reclami scritti dei clienti; gestione ricorsi ABF.',
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'SUP-HR',
                'macro_area' => 'Supporto',
                'name' => 'Risorse Umane (HR) e Formazione',
                'type' => 'Supporto',
                'description' => "Monitoraggio dell'aggiornamento professionale obbligatorio della rete e gestione contrattualistica del personale.",
                'outsourcable_status' => 'partial'
            ],
            [
                'code' => 'CTRL-COMPL',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Compliance (Conformità)',
                'type' => 'Controllo',
                'description' => "Verifica che l'azienda operi nel rispetto delle norme. Prevenzione sanzioni.",
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'CTRL-RISK',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Risk Management',
                'type' => 'Controllo',
                'description' => 'Identificazione, misurazione e mitigazione dei rischi operativi, reputazionali e informatici.',
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'CTRL-AML',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Antiriciclaggio (AML)',
                'type' => 'Controllo',
                'description' => "Profilatura rischio cliente, tenuta dell'AUI/Fascicoli, valutazione e invio Segnalazioni Operazioni Sospette (SOS).",
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'CTRL-AUDIT',
                'macro_area' => 'Controlli (III Livello)',
                'name' => 'Internal Audit (Revisione Interna)',
                'type' => 'Controllo',
                'description' => "Ispezioni indipendenti e test a campione su tutto l'impianto organizzativo aziendale. Riporta al CdA.",
                'outsourcable_status' => 'yes'
            ],
            [
                'code' => 'CTRL-DPO',
                'macro_area' => 'Controlli / Privacy',
                'name' => 'Data Protection Officer (DPO)',
                'type' => 'Controllo',
                'description' => "Vigila sull'osservanza del GDPR e funge da punto di contatto con il Garante Privacy.",
                'outsourcable_status' => 'yes'
            ],
        ];

        foreach ($funzioniAziendali as $item) {
            CompanyFunction::updateOrCreate(
                ['code' => $item['code']],  // Usa il code come chiave per evitare duplicati
                $item
            );
        }
    }
}
