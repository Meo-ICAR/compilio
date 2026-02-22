<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OamScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oamScopes = [
            ['code' => 'A.1', 'name' => 'Mutui'],
            ['code' => 'A.2', 'name' => 'Cessioni del V dello stipendio/pensione e delegazioni di pagamento'],
            ['code' => 'A.3', 'name' => 'Factoring crediti'],
            ['code' => 'A.4', 'name' => 'Acquisto di crediti'],
            ['code' => 'A.5', 'name' => 'Leasing autoveicoli e aeronavali'],
            ['code' => 'A.6', 'name' => 'Leasing immobiliare'],
            ['code' => 'A.7', 'name' => 'Leasing strumentale'],
            ['code' => 'A.8', 'name' => 'Leasing su fonti rinnovabili ed altre tipologie di investimento'],
            ['code' => 'A.9', 'name' => 'Aperture di credito in conto corrente'],
            ['code' => 'A.10', 'name' => 'Credito personale'],
            ['code' => 'A.11', 'name' => 'Credito finalizzato'],
            ['code' => 'A.12', 'name' => 'Prestito su pegno'],
            ['code' => 'A.13', 'name' => 'Rilascio di fidejussioni e garanzie'],
            ['code' => 'A.13-bis', 'name' => 'Garanzia collettiva dei fidi'],
            ['code' => 'A.14', 'name' => 'Anticipi e sconti commerciali'],
            ['code' => 'A.15', 'name' => 'Credito revolving'],
            ['code' => 'A.16', 'name' => 'Ristrutturazione dei crediti (art. 128-quater decies, del TUB)'],
        ];

        foreach ($oamScopes as $scope) {
            \App\Models\OamScope::firstOrCreate([
                'code' => $scope['code']
            ], [
                'name' => $scope['name']
            ]);
        }

        $this->command->info('OAM Scopes seeded successfully');
    }
}
