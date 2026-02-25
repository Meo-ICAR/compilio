<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAuditCompanyChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $checklist = DB::table('checklists')
            ->where('code', 'BANK_AUDIT')
            ->first();

        if (!$checklist) {
            $checklistId = DB::table('checklists')->insertGetId([
                'company_id' => null,  // Lasciato null per         'company_id' => null,  // Modello base disponibile nel sistema
                'name' => 'Verifica Ispettiva Mandante - Audit Banca su Mediatore',
                'code' => 'BANK_AUDIT',
                'type' => 'audit',
                'description' => 'Checklist utilizzata dalle Banche (Principals) per auditare annualmente la società di Mediazione Creditizia (Company) su Governance, AML, Privacy e Rete.',
                'principal_id' => null,  // Potrebbe essere valorizzato se si crea un template specifico per una singola banca
                'is_practice' => 0,
                'is_audit' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Creazione delle Domande / Voci di Audit
            $items = [
                // --- FASE 1: GOVERNANCE E REQUISITI OAM ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '10',
                    'name' => 'Visura Camerale Aggiornata',
                    'item_code' => 'bank_visura_company',
                    'question' => 'Caricare la Visura Camerale Ordinaria della società aggiornata agli ultimi 3 mesi.',
                    'description' => 'Verifica della compagine sociale, dei poteri di firma e assenza di procedure concorsuali.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',  // Il documento appartiene al Mediatore (Company)
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '20',
                    'name' => 'Attestazione Iscrizione OAM',
                    'item_code' => 'bank_oam_company',
                    'question' => 'Caricare il certificato o la contabile di pagamento del contributo annuale OAM della Società.',
                    'description' => "A prova del mantenimento dei requisiti di iscrizione all'elenco dei Mediatori Creditizi.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '30',
                    'name' => 'Polizza RC Professionale',
                    'item_code' => 'bank_rc_company',
                    'question' => 'Caricare copia della Polizza di Responsabilità Civile Professionale in corso di validità.',
                    'description' => 'Verificare che i massimali siano conformi alla normativa vigente in base ai volumi intermediati.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 2: ANTIRICICLAGGIO (AML) E PROCEDURE INTERNE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '40',
                    'name' => 'Manuale Antiriciclaggio',
                    'item_code' => 'bank_manuale_aml',
                    'question' => 'La società è dotata di un Manuale delle Procedure Antiriciclaggio (AML) aggiornato?',
                    'description' => 'Rispondere 1 (Vero) se presente e aggiornato, 0 (Falso) in caso di anomalie.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle Vero/Falso
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '50',
                    'name' => 'Copia Manuale AML',
                    'item_code' => 'bank_doc_manuale_aml',
                    'question' => 'Allegare copia del Manuale AML o del verbale del CdA di approvazione dello stesso.',
                    'description' => 'Il documento sarà esaminato dalla funzione Compliance della Banca.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'bank_manuale_aml',
                    'depends_on_value' => '1',
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '60',
                    'name' => 'Relazione Annuale Funzione Controllo',
                    'item_code' => 'bank_relazione_controlli',
                    'question' => 'Caricare la Relazione Annuale della Funzione di Controllo Interno / Compliance (se prevista per dimensioni).',
                    'description' => 'Inserire nelle note se la società è esentata per limiti dimensionali.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 3: PRIVACY, GDPR E IT SECURITY ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '70',
                    'name' => 'Nomina DPO',
                    'item_code' => 'bank_has_dpo',
                    'question' => 'La società ha nominato un Data Protection Officer (DPO)?',
                    'description' => 'Rispondere 1 (Vero) se nominato e comunicato al Garante.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '80',
                    'name' => 'Documentazione GDPR',
                    'item_code' => 'bank_doc_gdpr',
                    'question' => 'Caricare il Registro delle Attività di Trattamento e la policy di gestione dei Data Breach.',
                    'description' => 'Essenziale per garantire alla banca la sicurezza dei dati della clientela trasmessi.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 4: CONTROLLO RETE E TRASPARENZA ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '90',
                    'name' => 'Presenza Rete Agenti',
                    'item_code' => 'bank_has_rete',
                    'question' => 'La società si avvale di una rete di collaboratori / dipendenti a contatto con il pubblico?',
                    'description' => 'Rispondere 1 (Vero) se ci sono dipendenti/collaboratori, 0 (Falso) se opera solo il legale rappresentante.',
                    'is_required' => 1,
                    'n_documents' => 0,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '100',
                    'name' => 'Piano Ispezioni Rete',
                    'item_code' => 'bank_piano_ispezioni',
                    'question' => 'Caricare evidenza del piano di ispezioni effettuate dal Mediatore sui propri collaboratori.',
                    'description' => 'La Banca deve accertarsi che il Mediatore controlli la propria rete.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'company',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'bank_has_rete',
                    'depends_on_value' => '1',
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '110',
                    'name' => 'Materiale Pubblicitario',
                    'item_code' => 'bank_materiale_pubblicitario',
                    'question' => 'Il materiale pubblicitario utilizzato (sito web, social, volantini) menziona i prodotti della Banca nel rispetto delle linee guida di Trasparenza fornite?',
                    'description' => 'Caricare eventuali screenshot o PDF del materiale promozionale sottoposto a verifica.',
                    'is_required' => 1,
                    'n_documents' => 99,  // L'ispettore della banca carica le prove documentali (foto/screenshot)
                    'attach_model' => 'audit',  // Questo resta nell'audit, non va nell'anagrafica generica del mediatore
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 5: VERIFICA PRATICHE DELLA BANCA (TEST A CAMPIONE) ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '120',
                    'name' => 'Test Sostanziali su Pratiche Banca',
                    'item_code' => 'bank_test_pratiche',
                    'question' => "Sono state riscontrate anomalie (es. firme difformi, documenti alterati) sul campione di pratiche caricate verso questa Banca nell'ultimo semestre?",
                    'description' => 'Rispondere 1 (Vero) se ci sono anomalie, 0 (Falso) se il campione è regolare.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '130',
                    'name' => 'Dettaglio Anomalie Pratiche',
                    'item_code' => 'bank_dettaglio_anomalie',
                    'question' => 'Specificare i numeri di pratica coinvolti e il tipo di anomalia riscontrata.',
                    'description' => 'Campo testuale obbligatorio in caso di esito negativo del test a campione.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Campo testuale
                    'attach_model' => null,
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'bank_test_pratiche',
                    'depends_on_value' => '1',  // Mostrato solo se ci sono state anomalie
                ],
            ];

            // Mappatura automatica dei campi default
            $formattedItems = array_map(function ($item) use ($now) {
                return array_merge([
                    'answer' => null,
                    'annotation' => null,
                    'attach_model_id' => null,
                    'repeatable_code' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $item);
            }, $items);

            DB::table('checklist_items')->insert($formattedItems);
        }
    }
}
