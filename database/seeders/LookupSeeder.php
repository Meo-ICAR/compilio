<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        // Address Types
        $addressTypes = ['Residenza', 'Domicilio', 'Domicilio Legale', 'Domicilio Operativo', 'Sede Legale', 'Sede Operativa'];
        foreach ($addressTypes as $type) {
            \App\Models\AddressType::firstOrCreate(['name' => $type]);
        }

        // Client Types
        $clientTypes = ['Dipendente Pubblico',
            'Dipendente Privato',
            'Pensionato',
            'Privato Consumatore'];
        $companyTypes = [
            'Autonomo', 'Ditta Individuale', 'Libero Professionista', 'Amministratore',
            'Titolare', 'Socio'
        ];
        foreach ($clientTypes as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type, 'is_person' => true, 'is_company' => false]);
        }
        foreach ($companyTypes as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type, 'is_person' => false, 'is_company' => true]);
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
            // --- IDENTITÀ E ANAGRAFICA ---
            ['name' => "Carta d'Identità (Fronte/Retro)", 'scopes' => [$privacyScope->id, $amlScope->id, $istruttoriaScope->id]],
            ['name' => 'Patente', 'scopes' => [$privacyScope->id, $amlScope->id, $istruttoriaScope->id]],
            ['name' => 'Passaporto', 'scopes' => [$privacyScope->id, $amlScope->id, $istruttoriaScope->id]],
            ['name' => 'Codice Fiscale / Tessera Sanitaria', 'scopes' => [$privacyScope->id, $istruttoriaScope->id]],
            // --- PRIVACY E ANTIRICICLAGGIO (Compliance) ---
            ['name' => 'Informativa Privacy e Consenso Trattamento Dati', 'scopes' => [$privacyScope->id]],
            ['name' => 'Consenso al Trattamento Dati Particolari (Sanitari)', 'scopes' => [$privacyScope->id]],
            ['name' => 'Nomina incaricato del trattamento)', 'scopes' => [$privacyScope->id]],
            ['name' => 'Nomina responsabile del trattamento)', 'scopes' => [$privacyScope->id]],
            ['name' => 'Nomina amministratore di sistema', 'scopes' => [$privacyScope->id]],
            ['name' => 'Questionario Adeguata Verifica AML', 'scopes' => [$amlScope->id]],
            ['name' => 'Dichiarazione Titolare Effettivo', 'scopes' => [$amlScope->id]],
            ['name' => 'Dichiarazione PEP (Persona Esposta Politicamente)', 'scopes' => [$amlScope->id]],
            // --- TRASPARENZA E OAM ---
            ['name' => 'Lettera di Incarico di Mediazione', 'scopes' => [$oamScope->id]],
            ['name' => 'Avviso sulla Trasparenza (Presa Visione)', 'scopes' => [$oamScope->id]],
            ['name' => 'Modulo SECCI (Informazioni Europee di Base)', 'scopes' => [$oamScope->id, $istruttoriaScope->id]],
            ['name' => 'Modulo Segnalazione OAM', 'scopes' => [$oamScope->id]],
            // --- ISTRUTTORIA REDDITUALE (Dipendenti) ---
            ['name' => 'Ultime 3 Buste Paga', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Certificazione Unica (CU)', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Certificato di Stipendio / Attestato di Servizio', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Estratto Conto Contributivo INPS', 'scopes' => [$istruttoriaScope->id]],
            // --- ISTRUTTORIA REDDITUALE (Pensionati) ---
            ['name' => 'Cedolino Pensione', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Comunicazione di Quota Cedibile', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Modello Obis/M', 'scopes' => [$istruttoriaScope->id]],
            // --- DOCUMENTAZIONE AGGIUNTIVA ---
            ['name' => 'Conteggio Estintivo (per Rinnovi)', 'scopes' => [$istruttoriaScope->id]],
            ['name' => 'Rapporto di Visita Medica', 'scopes' => [$istruttoriaScope->id]],
        ];

        // Logica per l'inserimento nel database (esempio)
        foreach ($types as $type) {
            $documentType = \App\Models\DocumentType::updateOrCreate(['name' => $type['name']]);
            $documentType->scopes()->sync($type['scopes']);
        }

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
        \App\Models\EnasarcoLimit::firstOrCreate(['year' => 2026], ['name' => 'Massimali 2025', 'minimal_amount' => 1050, 'maximal_amount' => 46500]);

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
            ['name' => 'Documentale', 'code' => 'DOC', 'description' => 'Conservazione Documentale'],
            ['name' => 'Call Center', 'code' => 'CAL', 'description' => 'Call Center'],
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
