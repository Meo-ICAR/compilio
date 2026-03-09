<?php
namespace Database\Seeders;

use App\Models\DocumentScope;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Document Scopes
        $docScopes = [
            ['name' => 'Privacy', 'description' => 'GDPR Privacy Consent', 'color_code' => '#10B981'],
            ['name' => 'AML', 'description' => 'Anti-Money Laundering', 'color_code' => '#EF4444'],
            ['name' => 'OAM', 'description' => 'OAM Forms', 'color_code' => '#3B82F6'],
            ['name' => 'UIF', 'description' => 'UIF SOS', 'color_code' => '#3B82F6'],
            ['name' => 'Istruttoria', 'description' => 'Pratica docs', 'color_code' => '#F59E0B'],
            ['name' => 'Onboarding', 'description' => 'Onboarding', 'color_code' => '#F59E0B'],
            ['name' => 'Amministrativo', 'description' => 'Amministrativo', 'color_code' => '#F59E0B'],
        ];
        foreach ($docScopes as $scope) {
            \App\Models\DocumentScope::firstOrCreate(['name' => $scope['name']], $scope);
        }

        // 1. Recupero degli Scope
        $privacy = DocumentScope::where('name', 'Privacy')->first()?->id;
        $aml = DocumentScope::where('name', 'AML')->first()?->id;
        $oam = DocumentScope::where('name', 'OAM')->first()?->id;
        $uif = DocumentScope::where('name', 'UIF')->first()?->id;
        $istruttoria = DocumentScope::where('name', 'Istruttoria')->first()?->id;
        $onboarding = DocumentScope::where('name', 'Onboarding')->first()?->id;
        $amministrativo = DocumentScope::where('name', 'Amministrativo')->first()?->id;
        // 2. Definizione di tutti i 47 documenti con metadati tecnici
        $data = [
            // IDENTIFICAZIONE
            1 => ['slug' => 'carta-identita', 'regex' => '/carta.*identit|c\.i\.|identit/i', 'scopes' => [$privacy, $aml, $istruttoria]],
            2 => ['slug' => 'patente', 'regex' => '/patente/i', 'scopes' => [$privacy, $aml, $istruttoria]],
            3 => ['slug' => 'passaporto', 'regex' => '/passaporto/i', 'scopes' => [$privacy, $aml, $istruttoria]],
            4 => ['slug' => 'codice-fiscale', 'regex' => '/codice.*fiscale|tessera.*sanitaria|c\.f\./i', 'scopes' => [$privacy, $istruttoria]],
            // PRIVACY
            5 => ['slug' => 'privacy-informativa', 'regex' => '/informativa.*privacy|consenso.*dati/i', 'scopes' => [$privacy]],
            6 => ['slug' => 'consenso-sanitario', 'regex' => '/dati.*particolari|sanitari/i', 'scopes' => [$privacy]],
            7 => ['slug' => 'nomina-incaricato', 'regex' => '/nomina.*incaricato/i', 'scopes' => [$privacy]],
            8 => ['slug' => 'nomina-responsabile', 'regex' => '/nomina.*responsabile/i', 'scopes' => [$privacy]],
            9 => ['slug' => 'nomina-amministratore', 'regex' => '/amministratore.*sistema/i', 'scopes' => [$privacy]],
            // AML
            10 => ['slug' => 'questionario-aml', 'regex' => '/adeguata.*verifica|questionario.*aml/i', 'scopes' => [$aml]],
            11 => ['slug' => 'titolare-effettivo', 'regex' => '/titolare.*effettivo/i', 'scopes' => [$aml]],
            12 => ['slug' => 'dichiarazione-pep', 'regex' => '/persona.*esposta.*politicamente|pep/i', 'scopes' => [$aml]],
            // MEDIAZIONE / TRASPARENZA
            13 => ['slug' => 'incarico-mediazione', 'regex' => '/lettera.*incarico|contratto.*mediazione/i', 'scopes' => [$oam]],
            14 => ['slug' => 'trasparenza-avviso', 'regex' => '/avviso.*trasparenza|principali.*diritti/i', 'scopes' => [$oam]],
            15 => ['slug' => 'trasparenza-web', 'regex' => '/trasparenza.*sito|foglio.*informativo/i', 'scopes' => [$oam]],
            16 => ['slug' => 'privacy-web', 'regex' => '/privacy.*sito|privacy.*policy/i', 'scopes' => [$oam]],
            17 => ['slug' => 'requisiti-art6', 'regex' => '/requisiti.*organizzativi|art.*6/i', 'scopes' => [$oam]],
            // PROCEDURE OAM (18-35)
            18 => ['slug' => 'proc-manuale-operativo', 'regex' => '/manuale.*operativo/i', 'scopes' => [$oam]],
            19 => ['slug' => 'proc-sistema-deleghe', 'regex' => '/sistema.*deleghe/i', 'scopes' => [$oam]],
            20 => ['slug' => 'proc-compliance-risk', 'regex' => '/compliance.*risk/i', 'scopes' => [$oam]],
            21 => ['slug' => 'proc-internal-audit', 'regex' => '/internal.*audit/i', 'scopes' => [$oam]],
            22 => ['slug' => 'proc-aml-verifica', 'regex' => '/verifica.*clientela/i', 'scopes' => [$oam, $aml]],
            23 => ['slug' => 'proc-aml-profilatura', 'regex' => '/profilatura.*rischio/i', 'scopes' => [$oam, $aml]],
            24 => ['slug' => 'proc-aml-sos', 'regex' => '/segnalazione.*sospette|sos/i', 'scopes' => [$oam, $aml]],
            25 => ['slug' => 'proc-aml-conservazione', 'regex' => '/conservazione.*dati/i', 'scopes' => [$oam, $aml]],
            26 => ['slug' => 'proc-trasparenza-precontrattuale', 'regex' => '/informativa.*precontrattuale/i', 'scopes' => [$oam]],
            27 => ['slug' => 'proc-controllo-pubblicita', 'regex' => '/controllo.*pubblicit/i', 'scopes' => [$oam]],
            28 => ['slug' => 'proc-reclami-ricezione', 'regex' => '/ricezione.*trattazione.*reclami/i', 'scopes' => [$oam]],
            29 => ['slug' => 'proc-reclami-info', 'regex' => '/informativa.*risoluzione.*reclami/i', 'scopes' => [$oam]],
            30 => ['slug' => 'proc-rete-selezione', 'regex' => '/selezione.*inserimento.*rete/i', 'scopes' => [$oam]],
            31 => ['slug' => 'proc-rete-formazione', 'regex' => '/formazione.*continua/i', 'scopes' => [$oam]],
            32 => ['slug' => 'proc-rete-controlli', 'regex' => '/controlli.*rete/i', 'scopes' => [$oam]],
            33 => ['slug' => 'proc-privacy-gdpr', 'regex' => '/gdpr.*data.*protection/i', 'scopes' => [$oam, $privacy]],
            34 => ['slug' => 'proc-business-continuity', 'regex' => '/business.*continuity|disaster.*recovery/i', 'scopes' => [$oam]],
            35 => ['slug' => 'proc-231-etica', 'regex' => '/modello.*231|codice.*etico/i', 'scopes' => [$oam]],
            // MODULISTICA E REDDITO
            36 => ['slug' => 'modulo-secci', 'regex' => '/secci|informazioni.*europee/i', 'scopes' => [$oam, $istruttoria]],
            37 => ['slug' => 'segnalazione-oam', 'regex' => '/segnalazione.*oam/i', 'scopes' => [$oam]],
            38 => ['slug' => 'buste-paga', 'regex' => '/busta.*paga/i', 'scopes' => [$istruttoria]],
            39 => ['slug' => 'cu', 'regex' => '/certificazione.*unica|modello.*cu/i', 'scopes' => [$istruttoria]],
            40 => ['slug' => 'certificato-stipendio', 'regex' => '/attestato.*servizio|certificato.*stipendio/i', 'scopes' => [$istruttoria]],
            41 => ['slug' => 'estratto-inps', 'regex' => '/estratto.*inps|contributivo/i', 'scopes' => [$istruttoria]],
            42 => ['slug' => 'cedolino-pensione', 'regex' => '/cedolino.*pensione/i', 'scopes' => [$istruttoria]],
            43 => ['slug' => 'quota-cedibile', 'regex' => '/quota.*cedibile/i', 'scopes' => [$istruttoria]],
            44 => ['slug' => 'modello-obism', 'regex' => '/obis/i', 'scopes' => [$istruttoria]],
            45 => ['slug' => 'conteggio-estintivo', 'regex' => '/conteggio.*estintivo/i', 'scopes' => [$istruttoria]],
            46 => ['slug' => 'visita-medica', 'regex' => '/visita.*medica/i', 'scopes' => [$istruttoria]],
            47 => ['slug' => 'transparency-doc', 'regex' => '/rilevazione.*tassi|tassi.*usura|tegm/i', 'scopes' => [$oam]],
            // --- INTEGRAZIONI AREA COLLABORATORI (Onboarding) ---
            46 => ['slug' => 'visita-medica', 'regex' => '/visita.*medica/i', 'scopes' => [$istruttoria]],
            47 => ['slug' => 'transparency-doc', 'regex' => '/rilevazione.*tassi|tassi.*usura|tegm/i', 'scopes' => [$oam]],
            // --- INTEGRAZIONI AREA COLLABORATORI (Onboarding & Admin) ---
            48 => ['name' => 'Visura Camerale', 'slug' => 'visura-camerale', 'regex' => '/visura.*camerale|camera.*commercio|registro.*imprese/i', 'scopes' => [$onboarding]],
            49 => ['name' => 'Casellario Giudiziale', 'slug' => 'casellario-giudiziale', 'regex' => '/casellario.*giudiziale|procura.*repubblica/i', 'scopes' => [$onboarding]],
            50 => ['name' => 'Carichi Pendenti', 'slug' => 'carichi-pendenti', 'regex' => '/carichi.*pendenti/i', 'scopes' => [$onboarding]],
            51 => ['name' => 'Attestato OAM / IVASS', 'slug' => 'attestato-professionale', 'regex' => '/attestato.*(oam|ivass)|prova.*valutativa|formazione.*professionale/i', 'scopes' => [$onboarding]],
            52 => ['name' => 'Polizza RC Professionale', 'slug' => 'polizza-rc', 'regex' => '/polizza.*rc|responsabilita.*civile|assicurativa/i', 'scopes' => [$onboarding]],
            53 => ['name' => 'Documento Identità e CF', 'slug' => 'identita-codice-fiscale', 'regex' => '/carta.*identita|passaporto|codice.*fiscale|tessera.*sanitaria/i', 'scopes' => [$onboarding]],
            54 => ['name' => 'Modulo IBAN', 'slug' => 'iban-coordinate', 'regex' => '/iban|coordinate.*bancarie|appoggio.*conto/i', 'scopes' => [$amministrativo]],
            55 => ['name' => 'Contratto Collaborazione Firmato', 'slug' => 'contratto-firmato', 'regex' => '/contratto.*collaborazione|scrittura.*privata|accordo.*firmato/i', 'scopes' => [$amministrativo]],
            56 => ['name' => 'Autocertificazione Antimafia', 'slug' => 'antimafia', 'regex' => '/antimafia|dichiarazione.*sostitutiva/i', 'scopes' => [$onboarding]],
            57 => ['name' => 'Relazione Interna SOS', 'slug' => 'relazione-sos', 'regex' => '/relazione.*sos|analisi.*sospetta/i', 'scopes' => [$uif], 'is_agent' => true, 'is_principal' => true, 'is_company' => true],
            58 => ['name' => 'Ricevuta Portale UIF', 'slug' => 'ricevuta-sos-uif', 'regex' => '/ricevuta.*uif|infostat.*sos/i', 'scopes' => [$uif], 'is_agent' => true, 'is_principal' => true, 'is_company' => true],
            59 => ['name' => 'Documento Identità e CF', 'slug' => 'identita-codice-fiscale', 'regex' => '/carta.*identita|passaporto|codice.*fiscale|tessera.*sanitaria/i', 'scopes' => [$onboarding], 'is_agent' => true, 'is_client' => true, 'is_principal' => true],
            60 => ['name' => 'Contratto Collaborazione', 'slug' => 'contratto-agent', 'regex' => '/contratto.*collaborazione|scrittura.*privata/i', 'scopes' => [$amministrativo], 'is_agent' => true, 'is_company' => true],
            61 => ['name' => 'Regolamento Privacy', 'slug' => 'regolamento-privacy', 'regex' => '/privacy|gdpr|regolamento.*privacy/i', 'scopes' => [$privacy], 'is_company' => true],
            62 => ['name' => 'Polizza RC Professionale', 'slug' => 'polizza-rc', 'regex' => '/polizza.*rc|responsabilita.*civile/i', 'scopes' => [$onboarding], 'is_agent' => true, 'is_principal' => true],
            63 => ['name' => 'Attestato OAM / IVASS', 'slug' => 'attestato-professionale', 'regex' => '/attestato.*(oam|ivass)/i', 'scopes' => [$onboarding], 'is_agent' => true, 'is_principal' => true],
        ];

        // 3. Esecuzione: Aggiornamento record esistenti
        foreach ($data as $id => $attr) {
            $type = DocumentType::find($id);
            if (!$type) {
                DocumentType::insert([
                    'id' => $id,
                    'name' => $attr['name'],
                    'slug' => $attr['slug'],
                    'regex' => $attr['regex'],
                    'priority' => $attr['priority'] ?? 1,
                    'is_agent' => $attr['is_agent'] ?? false,
                    'is_principal' => $attr['is_principal'] ?? false,
                    'is_client' => $attr['is_client'] ?? false,
                    'is_practice_target' => $attr['is_practice_target'] ?? false,
                    'is_company' => $attr['is_company'] ?? false,
                ]);
            } else {
                // Aggiorniamo solo i campi tecnici necessari all'automazione
                $updateData = [
                    'slug' => $attr['slug'],
                    'regex' => $attr['regex'],
                    'priority' => $attr['priority'] ?? 1,
                ];

                // Aggiungi i campi target se presenti
                if (isset($attr['is_agent']))
                    $updateData['is_agent'] = $attr['is_agent'];
                if (isset($attr['is_principal']))
                    $updateData['is_principal'] = $attr['is_principal'];
                if (isset($attr['is_client']))
                    $updateData['is_client'] = $attr['is_client'];
                if (isset($attr['is_practice_target']))
                    $updateData['is_practice_target'] = $attr['is_practice_target'];
                if (isset($attr['is_company']))
                    $updateData['is_company'] = $attr['is_company'];

                $type->update($updateData);

                // Sincronizzazione Scopes (Rimuove null e sincronizza)
                $scopes = array_filter($attr['scopes']);
                if (!empty($scopes)) {
                    $type->scopes()->syncWithoutDetaching($scopes);
                }
            }
        }
    }
}
