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
        $clientTypes = ['Privato Consumatore', 'Azienda', 'Ditta Individuale', 'Libero Professionista'];
        foreach ($clientTypes as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type]);
        }

        // Company Types
        $companyTypes = ['S.p.A.', 'S.r.l.', 'S.n.c.', 'S.a.s.', 'Ditta Individuale'];
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
            ['name' => 'Mutuo Ipotecario', 'oam_code' => 'M01'],
            ['name' => 'Cessione del Quinto', 'oam_code' => 'C05'],
            ['name' => 'Prestito Personale', 'oam_code' => 'P01'],
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
            ['name' => 'Banca d\'Italia', 'acronym' => 'BankIt'],
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
