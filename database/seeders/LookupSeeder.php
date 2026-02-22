<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        // Address Types
        $addressTypes = ['Residenza', 'Domicilio', 'Sede Legale', 'Sede Operativa'];
        foreach ($addressTypes as $type) {
            \App\Models\AddressType::firstOrCreate(['name' => $type]);
        }

        // Client Types
        $clientTypes = ['Dipendente Pubblico',
            'Dipendente Privato',
            'Pensionato',
            'Privato Consumatore', 'Autonomo', 'Azienda', 'Ditta Individuale', 'Libero Professionista'];
        foreach ($clientTypes as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type, 'is_person' => true]);
        }

        // Company Types
        $companyTypes = ['Mediatore', 'Call Center', 'Albergo', 'Software House'];
        foreach ($companyTypes as $type) {
            \App\Models\CompanyType::firstOrCreate(['name' => $type]);
        }

        // Document Scopes
        $docScopes = [
            ['name' => 'Privacy', 'description' => 'GDPR Privacy Consent', 'color_code' => '#10B981'],
            ['name' => 'AML', 'description' => 'Anti-Money Laundering', 'color_code' => '#EF4444'],
            ['name' => 'OAM', 'description' => 'OAM Forms', 'color_code' => '#3B82F6'],
            ['name' => 'Istruttoria', 'description' => 'Pratica docs', 'color_code' => '#F59E0B'],
        ];
        foreach ($docScopes as $scope) {
            \App\Models\DocumentScope::firstOrCreate(['name' => $scope['name']], $scope);
        }

        // Document Types & Scope Linking
        $privacyScope = \App\Models\DocumentScope::where('name', 'Privacy')->first();
        $amlScope = \App\Models\DocumentScope::where('name', 'AML')->first();
        $oamScope = \App\Models\DocumentScope::where('name', 'OAM')->first();
        $istruttoriaScope = \App\Models\DocumentScope::where('name', 'Istruttoria')->first();

        $types = [
            ['name' => "Carta d'Identità", 'scopes' => [$privacyScope->id, $amlScope->id]],
            ['name' => 'Codice Fiscale', 'scopes' => [$privacyScope->id]],
            ['name' => 'Modulo Privacy Firmato', 'scopes' => [$privacyScope->id]],
            ['name' => 'Questionario AML', 'scopes' => [$amlScope->id]],
            ['name' => 'Modulo Segnalazione OAM', 'scopes' => [$oamScope->id]],
            ['name' => 'Busta Paga', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Contratto di Lavoro', 'scopes' => [$istruttoriaScope->id]],
        ];

        foreach ($types as $t) {
            $type = \App\Models\DocumentType::firstOrCreate(['name' => $t['name']]);
            if (isset($t['scopes'])) {
                $type->scopes()->syncWithoutDetaching($t['scopes']);
            }
        }

        // Employment Types
        $employmentTypes = ['Dipendente Tempo Indeterminato', 'Dipendente Tempo Determinato', 'Autonomo', 'Pensionato'];
        foreach ($employmentTypes as $type) {
            \App\Models\EmploymentType::firstOrCreate(['name' => $type]);
        }

        // Enasarco Limits
        \App\Models\EnasarcoLimit::firstOrCreate(['year' => 2024], ['name' => 'Massimali 2024', 'minimal_amount' => 1000, 'maximal_amount' => 45000]);
        \App\Models\EnasarcoLimit::firstOrCreate(['year' => 2025], ['name' => 'Massimali 2025', 'minimal_amount' => 1050, 'maximal_amount' => 46500]);

        // Practice Scopes
        $practiceScopes = [
            ['name' => 'Mutui', 'oam_code' => 'A.1'],
            ['name' => 'Cessioni del V dello stipendio/pensione e delegazioni di pagamento', 'oam_code' => 'A.2'],
            ['name' => 'Factoring crediti', 'oam_code' => 'A.3'],
            ['name' => 'Acquisto di crediti', 'oam_code' => 'A.4'],
            ['name' => 'Leasing autoveicoli e aeronavali', 'oam_code' => 'A.5'],
            ['name' => 'Leasing immobiliare', 'oam_code' => 'A.6'],
            ['name' => 'Leasing strumentale', 'oam_code' => 'A.7'],
            ['name' => 'Leasing su fonti rinnovabili ed altre tipologie di investimento', 'oam_code' => 'A.8'],
            ['name' => 'Aperture di credito in conto corrente', 'oam_code' => 'A.9'],
            ['name' => 'Credito personale', 'oam_code' => 'A.10'],
            ['name' => 'Credito finalizzato', 'oam_code' => 'A.11'],
            ['name' => 'Prestito su pegno', 'oam_code' => 'A.12'],
            ['name' => 'Rilascio di fidejussioni e garanzie', 'oam_code' => 'A.13'],
            ['name' => 'Garanzia collettiva dei fidi', 'oam_code' => 'A.13-bis'],
            ['name' => 'Anticipi e sconti commerciali', 'oam_code' => 'A.14'],
            ['name' => 'Credito revolving', 'oam_code' => 'A.15'],
            ['name' => 'Ristrutturazione dei crediti (art. 128-quater decies, del TUB)', 'oam_code' => 'A.16'],
        ];
        foreach ($practiceScopes as $ps) {
            \App\Models\PracticeScope::firstOrCreate(['name' => $ps['name']], $ps);
        }

        // Software Categories
        $softwareCats = [
            ['name' => 'CRM', 'code' => 'CRM', 'description' => 'Customer Relationship Management'],
            ['name' => 'Contabilità', 'code' => 'ACC', 'description' => 'Sistemi Contabili'],
            ['name' => 'Firma Elettronica', 'code' => 'SIGN', 'description' => 'Servizi di Firma Digitale'],
        ];
        foreach ($softwareCats as $cat) {
            \App\Models\SoftwareCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // Software Applications (Depending on Category)
        $crmCat = \App\Models\SoftwareCategory::where('code', 'CRM')->first();
        if ($crmCat) {
            \App\Models\SoftwareApplication::firstOrCreate(['name' => 'Salesforce'], [
                'category_id' => $crmCat->id,
                'provider_name' => 'Salesforce Inc.',
                'is_cloud' => 1
            ]);
        }

        // Regulatory Bodies
        $bodies = [
            ['name' => 'OAM', 'acronym' => 'OAM'],
            ['name' => "Banca d'Italia", 'acronym' => 'BankIt'],
            ['name' => 'Garante Privacy', 'acronym' => 'GPDP'],
        ];
        foreach ($bodies as $body) {
            \App\Models\RegulatoryBody::firstOrCreate(['name' => $body['name']], $body);
        }

        // Abi (Banks lookup)
        $abis = [
            ['abi' => '03069', 'name' => 'Intesa Sanpaolo S.p.A.', 'type' => 'BANCA'],
            ['abi' => '02008', 'name' => 'UniCredit S.p.A.', 'type' => 'BANCA'],
            ['abi' => '10601', 'name' => 'Compass Banca S.p.A.', 'type' => 'BANCA'],
        ];
        foreach ($abis as $abi) {
            \App\Models\Abi::firstOrCreate(['abi' => $abi['abi']], $abi);
        }
    }
}
