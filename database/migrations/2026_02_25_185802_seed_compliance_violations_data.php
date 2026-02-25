<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $violations = [
            [
                'company_id' => null,  // Violazione globale
                'user_id' => null,
                'violatable_type' => null,
                'violatable_id' => null,
                'violation_type' => 'accesso_non_autorizzato',
                'severity' => 'alto',
                'description' => 'Tentativo di accesso a dati clienti da parte di utente non autorizzato. IP rilevato da rete esterna.',
                'affected_subjects_count' => 15,
                'likely_consequences' => 'Possibile esposizione dati personali e finanziari dei clienti coinvolti.',
                'discovery_date' => $now->subDays(2),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => 'Rischio valutato come improbabile per i diritti degli interessati',
                'are_subjects_notified' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => null,
                'created_at' => $now->subDays(2),
                'updated_at' => $now->subDays(2),
            ],
            [
                'company_id' => null,
                'user_id' => 1,
                'violatable_type' => 'App\Models\Client',
                'violatable_id' => 123,
                'violation_type' => 'kyc_scaduto',
                'severity' => 'medio',
                'description' => 'Documentazione KYC scaduta per cliente. Manca aggiornamento documenti identità.',
                'affected_subjects_count' => 1,
                'likely_consequences' => 'Impossibilità di proseguire con istruttoria finanziaria fino ad aggiornamento.',
                'discovery_date' => $now->subHours(6),
                'ip_address' => '10.0.0.15',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => true,
                'resolved_at' => $now->subHours(2),
                'resolved_by' => 1,
                'resolution_notes' => 'Cliente contattato e documentazione in via di aggiornamento.',
                'created_at' => $now->subHours(6),
                'updated_at' => $now->subHours(2),
            ],
            [
                'company_id' => null,
                'user_id' => null,
                'violatable_type' => null,
                'violatable_id' => null,
                'violation_type' => 'data_breach',
                'severity' => 'critico',
                'description' => 'Potenziale data breach rilevato da sistema di monitoraggio. Traffico anomalo su database clienti.',
                'affected_subjects_count' => 250,
                'likely_consequences' => 'Esposizione massiva dati personali con rischio di furto identità e frodi.',
                'discovery_date' => $now->subMinutes(30),
                'ip_address' => '185.220.101.182',
                'user_agent' => 'curl/7.68.0',
                'is_dpa_notified' => true,
                'dpa_notified_at' => $now->subMinutes(15),
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => 'Indagine in corso. Sistema isolato e analisi forense avviata.',
                'created_at' => $now->subMinutes(30),
                'updated_at' => $now->subMinutes(30),
            ],
            [
                'company_id' => null,
                'user_id' => 3,
                'violatable_type' => 'App\Models\Practice',
                'violatable_id' => 456,
                'violation_type' => 'forzatura_stato',
                'severity' => 'basso',
                'description' => 'Tentativo di modifica stato pratica da non autorizzato a completato.',
                'affected_subjects_count' => 0,
                'likely_consequences' => null,
                'discovery_date' => $now->subDay(),
                'ip_address' => '172.16.0.25',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => false,
                'resolved_at' => $now->subHours(12),
                'resolved_by' => 2,
                'resolution_notes' => "Accesso revocato all'utente. Pratica ripristinata stato corretto.",
                'created_at' => $now->subDay(),
                'updated_at' => $now->subHours(12),
            ],
        ];

        DB::table('compliance_violations')->insert($violations);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('compliance_violations')->truncate();
    }
};
