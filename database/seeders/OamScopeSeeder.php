<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OamScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pulisce la tabella prima di inserire
        DB::table('oam_scopes')->delete();

        // Inserisce direttamente i dati
        $scopes = [
            [
                'id' => 1,
                'code' => 'A.1',
                'name' => 'Mutui',
                'description' => 'A.1 Mutui',
                'tipo_prodotto' => '["Mutuo"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:53:30',
            ],
            [
                'id' => 2,
                'code' => 'A.2',
                'name' => 'Cessioni del V dello stipendio/pensione e delegazioni di pagamento',
                'description' => 'A.2 Cessioni del V dello stipendio/pensione e delegazioni di pagamento',
                'tipo_prodotto' => '["Cessione","Delega"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:47:47',
            ],
            [
                'id' => 3,
                'code' => 'A.3',
                'name' => 'Factoring crediti',
                'description' => 'A.3 Factoring crediti',
                'tipo_prodotto' => '["Factoring"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:46:53',
            ],
            [
                'id' => 4,
                'code' => 'A.4',
                'name' => 'Acquisto di crediti',
                'description' => 'A.4 Acquisto di crediti',
                'tipo_prodotto' => '["Aziendale"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:53:46',
            ],
            [
                'id' => 5,
                'code' => 'A.4 bis',
                'name' => 'Anticipazione TFS',
                'description' => 'A.4 bis Anticipazione TFS',
                'tipo_prodotto' => '["TFS"]',
                'created_at' => '2026-03-23 09:21:28',
                'updated_at' => '2026-03-23 11:54:07',
            ],
            [
                'id' => 6,
                'code' => 'A.5',
                'name' => 'Leasing autoveicoli e aeronavali',
                'description' => 'A.5 Leasing autoveicoli e aeronavali',
                'tipo_prodotto' => '["Leasing"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:46:53',
            ],
            [
                'id' => 7,
                'code' => 'A.6',
                'name' => 'Leasing immobiliare',
                'description' => 'A.6 Leasing immobiliare',
                'tipo_prodotto' => '["Leasing"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:46:53',
            ],
            [
                'id' => 8,
                'code' => 'A.7',
                'name' => 'Leasing strumentale',
                'description' => 'A.7 Leasing strumentale',
                'tipo_prodotto' => '["Leasing"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 11:46:53',
            ],
            [
                'id' => 9,
                'code' => 'A.8',
                'name' => 'Leasing su fonti rinnovabili ed altre tipologie di investimento',
                'description' => 'A.8 Leasing su fonti rinnovabili ed altre tipologie di investimento',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 10,
                'code' => 'A.9',
                'name' => 'Aperture di credito in conto corrente',
                'description' => 'A.9 Aperture di credito in conto corrente',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 11,
                'code' => 'A.10',
                'name' => 'Credito personale',
                'description' => 'A.10 Credito personale',
                'tipo_prodotto' => '["Prestito"]',
                'created_at' => '2026-03-23 09:15:48',
                'updated_at' => '2026-03-23 09:15:48',
            ],
            [
                'id' => 12,
                'code' => 'A.11',
                'name' => 'Credito finalizzato',
                'description' => 'A.11 Credito finalizzato',
                'tipo_prodotto' => '["Prestito"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 13,
                'code' => 'A.12',
                'name' => 'Prestito su pegno',
                'description' => 'A.12 Prestito su pegno',
                'tipo_prodotto' => '["Prestito"]',
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 14,
                'code' => 'A.13',
                'name' => 'Rilascio di fidejussioni e garanzie',
                'description' => 'A.13 Rilascio di fidejussioni e garanzie',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 15,
                'code' => 'A.13-bis',
                'name' => 'Garanzia collettiva dei fidi',
                'description' => 'A.13-bis Garanzia collettiva dei fidi',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 16,
                'code' => 'A.14',
                'name' => 'Anticipi e sconti commerciali',
                'description' => 'A.14 Anticipi e sconti commerciali',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 17,
                'code' => 'A.15',
                'name' => 'Credito revolving',
                'description' => 'A.15 Credito revolving',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 18,
                'code' => 'A.16',
                'name' => 'Ristrutturazione dei crediti (art. 128-quater decies, del TUB)',
                'description' => 'A.16 Ristrutturazione dei crediti (art. 128-quater decies, del TUB)',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 19,
                'code' => 'Consulenza',
                'name' => ' ',
                'description' => 'Consulenza  ',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
            [
                'id' => 20,
                'code' => 'Segnalazione mutuo',
                'name' => ' ',
                'description' => 'Segnalazione mutuo  ',
                'tipo_prodotto' => null,
                'created_at' => '2026-03-23 09:06:44',
                'updated_at' => '2026-03-23 09:06:44',
            ],
        ];

        DB::table('oam_scopes')->insert($scopes);

        $this->command->info('OAM Scopes seeded successfully');

        // Practices section rimane invariata
        $practices = [
            // --- MUTUI ---
            ['id' => 1, 'name' => 'Mutui', 'code' => 'MUT', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Mutuo', 'is_oneclient' => 0],
            ['id' => 34, 'name' => 'IPOTECARIO', 'code' => 'MUT_IPOTECARIO', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Mutuo', 'is_oneclient' => 0],
            // --- CESSIONI ---
            ['id' => 2, 'name' => 'Cessioni del V dello stipendio', 'code' => 'CessioneCQS', 'oam_code' => 'A.2', 'tipo_prodotto' => 'Cessione', 'is_oneclient' => 1],
            ['id' => 3, 'name' => 'Cessioni del V pensione', 'code' => 'CessioneCQP', 'oam_code' => 'A.2', 'tipo_prodotto' => 'Cessione', 'is_oneclient' => 1],
            // --- DELEGA ---
            ['id' => 4, 'name' => 'Delegazioni di pagamento', 'code' => 'Delega', 'oam_code' => 'A.2', 'tipo_prodotto' => 'Delega', 'is_oneclient' => 1],
            // --- PRESTITI ---
            ['id' => 12, 'name' => 'Credito personale', 'code' => 'CRED_PERS', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 13, 'name' => 'Credito finalizzato', 'code' => 'CRED_FIN', 'oam_code' => 'A.11', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 29, 'name' => 'Chirografario', 'code' => 'Chirografario', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 30, 'name' => 'Microcredito', 'code' => 'Microcredito', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 11, 'name' => 'Aperture di credito in conto corrente', 'code' => 'APERT_CCC', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Aziendale', 'is_oneclient' => 1],
            ['id' => 12, 'name' => 'Consulenza', 'code' => 'CONSULENZA', 'oam_code' => '', 'tipo_prodotto' => 'consulenza', 'is_oneclient' => 1],
            ['id' => 13, 'name' => 'Segnalazione mutuo', 'code' => 'MUTUOSEG', 'oam_code' => '', 'tipo_prodotto' => 'Mutux', 'is_oneclient' => 1],
        ];
        foreach ($practices as $practice) {
            DB::table('practice_scopes')->updateOrInsert(
                ['id' => $practice['id']],
                array_merge($practice, [
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('practice Scopes seeded successfully');
    }
}
