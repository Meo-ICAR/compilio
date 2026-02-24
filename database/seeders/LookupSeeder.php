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

        $registroTrattamentiPartner = [
            'Agente Immobiliare' => [
                'Ruolo Privacy' => 'Titolare Autonomo o Contitolare',
                'Finalità' => "Collaborazione per l'istruttoria di mutui ipotecari legati a compravendite immobiliari.",
                'Categorie di Interessati' => 'Clienti (Acquirenti/Richiedenti mutuo)',
                'Categorie di Dati Trattati' => "Anagrafici, Contatto, Documenti Identità, Dati dell'immobile",
                'Tempi di Conservazione' => "10 anni dall'erogazione (obblighi AML/Civilistici) o 12 mesi se respinta.",
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Scambio documenti tramite piattaforme sicure/CRM, crittografia allegati.'
            ],
            'Commercialista / Esperto Contabile' => [
                'Ruolo Privacy' => 'Titolare Autonomo',
                'Finalità' => "Acquisizione documentazione reddituale e fiscale per l'istruttoria di finanziamenti aziendali o personali.",
                'Categorie di Interessati' => 'Clienti, Amministratori, Soci, Titolari Effettivi',
                'Categorie di Dati Trattati' => 'Anagrafici, Documenti Identità, Dati Economici e Finanziari (Bilanci, Unico, CUD)',
                'Tempi di Conservazione' => '10 anni dalla chiusura del rapporto.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Accesso limitato per competenza, invio tramite PEC o portale sicuro.'
            ],
            'Consulente del Lavoro' => [
                'Ruolo Privacy' => 'Responsabile Esterno / Titolare Autonomo',
                'Finalità' => 'Acquisizione certificazioni stipendiali e TFR per pratiche di Cessione del Quinto e Prestiti Personali.',
                'Categorie di Interessati' => 'Clienti (Dipendenti)',
                'Categorie di Dati Trattati' => 'Anagrafici, Documenti Identità, Dati Economici (Buste paga, Certificati di stipendio)',
                'Tempi di Conservazione' => "10 anni dall'estinzione del finanziamento.",
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Scambio dati cifrato, password protection sui PDF.'
            ],
            'Consulente Aziendale' => [
                'Ruolo Privacy' => 'Responsabile Esterno',
                'Finalità' => 'Supporto tecnico per redazione Business Plan e richieste di credito Corporate.',
                'Categorie di Interessati' => 'Clienti (Amministratori, Garanti)',
                'Categorie di Dati Trattati' => 'Anagrafici, Dati Economici e Finanziari (Piani industriali, flussi di cassa, CRIF)',
                'Tempi di Conservazione' => '10 anni dalla chiusura della pratica.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Accordo di riservatezza (NDA), accessi segregati al CRM.'
            ],
            'Assicuratore' => [
                'Ruolo Privacy' => 'Titolare Autonomo o Responsabile Esterno',
                'Finalità' => "Emissione polizze CPI (Credit Protection Insurance) collegate all'erogazione del credito.",
                'Categorie di Interessati' => 'Clienti (Assicurati)',
                'Categorie di Dati Trattati' => 'Anagrafici, Economici, Categorie Particolari (Dati sanitari, questionari anamnestici)',
                'Tempi di Conservazione' => '10 anni dalla scadenza della polizza.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Buste chiuse per dati sanitari, archivi ad accesso ristretto ai soli incaricati.'
            ],
            "Procacciatore d'Affari" => [
                'Ruolo Privacy' => 'Responsabile Esterno / Incaricato',
                'Finalità' => 'Segnalazione di nominativi interessati a prodotti di credito (Lead Generation).',
                'Categorie di Interessati' => 'Potenziali Clienti (Prospect)',
                'Categorie di Dati Trattati' => 'Anagrafici, Contatto',
                'Tempi di Conservazione' => '24 mesi per finalità commerciali (o fino a revoca del consenso).',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Formazione privacy obbligatoria, divieto di conservazione locale dei dati.'
            ],
            'Concessionario Auto' => [
                'Ruolo Privacy' => 'Responsabile Esterno o Contitolare',
                'Finalità' => "Istruttoria per prestiti finalizzati all'acquisto di veicoli e Leasing targato.",
                'Categorie di Interessati' => 'Clienti (Acquirenti)',
                'Categorie di Dati Trattati' => 'Anagrafici, Documenti Identità, Economici (Preventivi, IBAN, Reddito base)',
                'Tempi di Conservazione' => "10 anni dall'erogazione.",
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Caricamento diretto a portale, divieto di fotocopie non custodite.'
            ],
            'Call Center (Telemarketing / Qualifica Lead)' => [
                'Ruolo Privacy' => 'Responsabile Esterno (Art. 28)',
                'Finalità' => 'Contatto telefonico per pre-qualifica commerciale e fissazione appuntamenti.',
                'Categorie di Interessati' => 'Potenziali Clienti e Clienti in portafoglio',
                'Categorie di Dati Trattati' => 'Anagrafici, Contatto, Dati Economici di base',
                'Tempi di Conservazione' => 'Max 12 mesi per lead non convertiti, 24 mesi per marketing con consenso.',
                'Trasferimento Extra-UE' => 'Possibile (Verificare sede server dialer)',
                'Misure di Sicurezza' => 'Sistemi di masking del numero telefonico, divieto di export database.'
            ],
            'List Provider (Fornitore Liste Contatti)' => [
                'Ruolo Privacy' => 'Titolare Autonomo',
                'Finalità' => 'Acquisizione liste anagrafiche consensate per campagne di marketing diretto.',
                'Categorie di Interessati' => 'Potenziali Clienti (Prospect)',
                'Categorie di Dati Trattati' => 'Anagrafici, Contatto',
                'Tempi di Conservazione' => 'Fino a revoca del consenso o opt-out.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Verifica preventiva della validità dei consensi (Audit fornitori).'
            ],
            'Software House (Sviluppo Gestionale / CRM)' => [
                'Ruolo Privacy' => 'Responsabile Esterno (Art. 28)',
                'Finalità' => 'Sviluppo, manutenzione, assistenza tecnica e hosting del gestionale pratiche cloud.',
                'Categorie di Interessati' => 'Tutti (Clienti, Prospect, Collaboratori, Dipendenti)',
                'Categorie di Dati Trattati' => 'TUTTI (Anagrafici, Identità, Economici, Sanitari, Minori, Log di navigazione)',
                'Tempi di Conservazione' => 'Per tutta la durata del contratto SaaS + 30 giorni per distruzione backup.',
                'Trasferimento Extra-UE' => 'Sì/No (Dipende dal provider cloud).',
                'Misure di Sicurezza' => 'Crittografia at-rest e in-transit (TLS), ISO 27001, Backup immutabili.'
            ],
            'Consulente Informatico / Sistemista (Gestione Server/Rete)' => [
                'Ruolo Privacy' => 'Amministratore di Sistema / Responsabile Esterno',
                'Finalità' => 'Gestione infrastruttura hardware/software, reti LAN, backup e disaster recovery.',
                'Categorie di Interessati' => 'Clienti, Dipendenti',
                'Categorie di Dati Trattati' => 'Dati di Navigazione, Log di Sistema, IP, Accesso incidentale ai database in chiaro.',
                'Tempi di Conservazione' => 'Log di sistema conservati per almeno 6 mesi.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Autenticazione a due fattori (2FA) obbligatoria, tracciamento log amministratori.'
            ],
            'Avvocato' => [
                'Ruolo Privacy' => 'Titolare Autonomo',
                'Finalità' => 'Gestione pratiche di Saldo e Stralcio, esdebitamento, o istruttoria in presenza di contenziosi.',
                'Categorie di Interessati' => 'Clienti, Controparti, Creditori',
                'Categorie di Dati Trattati' => 'Anagrafici, Economici, Categorie Particolari (Dati giudiziari, sentenze)',
                'Tempi di Conservazione' => '10 anni dalla definizione della vertenza.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Segreto professionale, archivi fisici e digitali protetti.'
            ],
            'Notaio' => [
                'Ruolo Privacy' => 'Titolare Autonomo',
                'Finalità' => 'Stipula atti pubblici per mutui, surroghe, costituzione garanzie ipotecarie e procure.',
                'Categorie di Interessati' => 'Clienti (Richiedenti, Datori di ipoteca)',
                'Categorie di Dati Trattati' => 'Anagrafici, Documenti Identità, Dati Economico-Finanziari e Patrimoniali',
                'Tempi di Conservazione' => 'Illimitata per gli atti a rogito / 10 anni per documenti in possesso del Mediatore.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Canali crittografati dedicati (Rete Nazionale Notariato).'
            ],
            'Perito Immobiliare' => [
                'Ruolo Privacy' => 'Responsabile Esterno o Titolare Autonomo',
                'Finalità' => "Sopralluogo e redazione perizia estimativa dell'immobile a garanzia del finanziamento.",
                'Categorie di Interessati' => 'Clienti (Proprietari/Acquirenti)',
                'Categorie di Dati Trattati' => 'Anagrafici, Contatti, Dati catastali, Planimetrie',
                'Tempi di Conservazione' => '10 anni dalla redazione della perizia.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Invio perizie tramite portali bancari sicuri o file protetti da password.'
            ],
            'Amministratore di Condominio' => [
                'Ruolo Privacy' => 'Titolare Autonomo',
                'Finalità' => 'Fornitura documentazione per istruttoria finanziamenti destinati a lavori condominiali.',
                'Categorie di Interessati' => 'Condòmini',
                'Categorie di Dati Trattati' => 'Anagrafici, Economico-Finanziari (Ripartizione millesimale, delibere)',
                'Tempi di Conservazione' => '10 anni.',
                'Trasferimento Extra-UE' => 'No',
                'Misure di Sicurezza' => 'Comunicazione limitata ai soli dati pertinenti alla pratica di finanziamento.'
            ]
        ];

        foreach ($registroTrattamentiPartner as $figura => $dati) {
            // Usiamo updateOrCreate per evitare duplicati se lanci il seeder più volte
            \App\Models\ClientType::updateOrCreate(
                ['name' => $figura],
                [
                    'is_company' => true,
                    'privacy_role' => $dati['Ruolo Privacy'],
                    'purpose' => $dati['Finalità'],
                    'data_subjects' => $dati['Categorie di Interessati'],
                    'data_categories' => $dati['Categorie di Dati Trattati'],
                    'retention_period' => $dati['Tempi di Conservazione'],
                    'extra_eu_transfer' => $dati['Trasferimento Extra-UE'],
                    'security_measures' => $dati['Misure di Sicurezza'],
                ]
            );
        }

        foreach ($clientTypes as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type, 'is_person' => false, 'is_company' => null]);
        }
        foreach ($companyTypes as $type) {
            \App\Models\ClientType::firstOrCreate(['name' => $type, 'is_person' => false, 'is_company' => null]);
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
        $identificazioneScope = 'Identificazione';
        $privacyCode = 'Privacy';
        $amlCode = 'AML';
        $oamCode = 'OAM';
        $istruttoriaCode = 'Istruttoria';
        $types = [
            // --- IDENTITÀ E ANAGRAFICA ---
            ['name' => "Carta d'Identità (Fronte/Retro)", 'code' => $identificazioneScope, 'scopes' => [$privacyScope->id, $amlScope->id, $istruttoriaScope->id]],
            ['name' => 'Patente', 'code' => $identificazioneScope, 'scopes' => [$privacyScope->id, $amlScope->id, $istruttoriaScope->id]],
            ['name' => 'Passaporto', 'code' => $identificazioneScope, 'scopes' => [$privacyScope->id, $amlScope->id, $istruttoriaScope->id]],
            ['name' => 'Codice Fiscale / Tessera Sanitaria', 'code' => $identificazioneScope, 'scopes' => [$privacyScope->id, $istruttoriaScope->id]],
            // --- PRIVACY E ANTIRICICLAGGIO (Compliance) ---
            ['name' => 'Informativa Privacy e Consenso Trattamento Dati', 'code' => $privacyCode, 'scopes' => [$privacyScope->id]],
            ['name' => 'Consenso al Trattamento Dati Particolari (Sanitari)', 'code' => $privacyCode, 'scopes' => [$privacyScope->id]],
            ['name' => 'Nomina incaricato del trattamento)', 'code' => $privacyCode, 'scopes' => [$privacyScope->id]],
            ['name' => 'Nomina responsabile del trattamento)', 'code' => $privacyCode, 'scopes' => [$privacyScope->id]],
            ['name' => 'Nomina amministratore di sistema', 'code' => $privacyCode, 'scopes' => [$privacyScope->id]],
            ['name' => 'Questionario Adeguata Verifica AML', 'code' => $amlCode, 'scopes' => [$amlScope->id]],
            ['name' => 'Dichiarazione Titolare Effettivo', 'code' => $amlScope, 'scopes' => [$amlScope->id]],
            ['name' => 'Dichiarazione PEP (Persona Esposta Politicamente)', 'code' => $amlCode, 'scopes' => [$amlScope->id]],
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

        foreach ($types as $t) {
            $type = \App\Models\DocumentType::firstOrCreate(['name' => $t['name']]);
            if (isset($t['code'])) {
                $type->code = $t['code'];
                $type->save();
            }
            if (isset($t['scopes'])) {
                $type->scopes()->syncWithoutDetaching($t['scopes']);
            }
        }

        $ruoliPrivacy = [
            'Operatore Front-Office' => [
                'privacy_role' => 'Soggetto Autorizzato (Incaricato)',
                'purpose' => 'Primo contatto, accoglienza clienti, presa appuntamenti e smistamento lead/chiamate.',
                'data_subjects' => 'Clienti e Potenziali Clienti (Prospect)',
                'data_categories' => 'Dati Anagrafici e di Contatto (Nome, Cognome, Telefono, Email)',
                'retention_period' => 'Max 12 mesi per prospect non convertiti, o fino al passaggio al consulente.',
                'extra_eu_transfer' => 'No',
                'security_measures' => 'Accesso limitato alle sole anagrafiche base del CRM, password nominali.'
            ],
            'Consulente Commerciale' => [
                'privacy_role' => 'Soggetto Autorizzato (Incaricato)',
                'purpose' => 'Consulenza creditizia, profilazione esigenze, raccolta consensi privacy e documentazione preliminare per istruttoria.',
                'data_subjects' => 'Clienti (Richiedenti, Co-obbligati, Garanti)',
                'data_categories' => "Anagrafici, Contatti, Documenti d'Identità, Dati Reddituali e Familiari base",
                'retention_period' => "10 anni dall'erogazione (legato alla pratica) o fino a revoca/rifiuto.",
                'extra_eu_transfer' => 'No',
                'security_measures' => 'Dispositivi protetti (MFA), divieto di salvataggio dati su supporti rimovibili non crittografati.'
            ],
            'Addetto Backoffice' => [
                'privacy_role' => 'Soggetto Autorizzato (Incaricato)',
                'purpose' => 'Data entry, verifica conformità documentale, caricamento pratiche sui portali bancari (B2B).',
                'data_subjects' => 'Clienti, Garanti, Datori di Lavoro (per verifiche)',
                'data_categories' => 'Anagrafici, Documenti Identità, Dati Reddituali (Buste paga, CUD, Unico), Dati Bancari (IBAN)',
                'retention_period' => '10 anni per obblighi civilistici e amministrativi.',
                'extra_eu_transfer' => 'No',
                'security_measures' => 'Accesso segregato ai portali bancari, tracciamento log di visualizzazione e modifica.'
            ],
            'Analista Istruttoria' => [
                'privacy_role' => 'Soggetto Autorizzato (Incaricato)',
                'purpose' => 'Analisi di fattibilità, interrogazione banche dati creditizie (SIC, CRIF), valutazione del rischio e pre-delibera.',
                'data_subjects' => 'Richiedenti, Co-obbligati, Garanti',
                'data_categories' => 'Dati Finanziari, Storico Creditizio (Esposizioni, rate, ritardi), Estratti Conto',
                'retention_period' => "Tempi previsti dai codici di condotta SIC (da 6 a 36 mesi in base all'esito del credito).",
                'extra_eu_transfer' => 'No',
                'security_measures' => 'Accessi nominali tracciati ai SIC (Sistemi di Informazione Creditizia), VPN per eventuale smart working.'
            ],
            'Amministrazione e CRM' => [
                'privacy_role' => 'Soggetto Autorizzato (Incaricato)',
                'purpose' => 'Fatturazione, liquidazione provvigioni, gestione campagne di marketing, newsletter e data analytics.',
                'data_subjects' => 'Clienti, Collaboratori, Partner',
                'data_categories' => 'Dati Contabili e Fiscali, Dati di Contatto, Storico interazioni CRM',
                'retention_period' => '10 anni per obblighi fiscali / 24 mesi max per finalità di marketing.',
                'extra_eu_transfer' => 'Possibile (Verificare clausole se tool di Email Marketing è Extra-UE).',
                'security_measures' => 'Pseudonimizzazione per analisi dati, gestione automatizzata opt-out marketing.'
            ],
            'Compliance & AML' => [
                'privacy_role' => 'Soggetto Autorizzato / Responsabile Antiriciclaggio',
                'purpose' => 'Adeguata verifica della clientela (KYC), profilazione rischio riciclaggio, gestione SOS (Segnalazione Operazioni Sospette).',
                'data_subjects' => 'Clienti, Titolari Effettivi, Amministratori di società',
                'data_categories' => 'Identità, Dati Patrimoniali, Categorie Particolari (Info su condanne penali, sanzioni, qualifica PEP).',
                'retention_period' => '10 anni dalla cessazione del rapporto continuativo (Normativa Antiriciclaggio).',
                'extra_eu_transfer' => 'No',
                'security_measures' => 'Archivio Unico Informatico (AUI) con accesso ultra-ristretto ai soli delegati AML, database crittografato.'
            ],
            'Responsabile di Filiale' => [
                'privacy_role' => 'Designato / Referente Interno Privacy',
                'purpose' => 'Supervisione operativa della filiale, risoluzione escalation, gestione reclami, monitoraggio KPI collaboratori.',
                'data_subjects' => 'Clienti, Collaboratori della filiale',
                'data_categories' => 'Tutti i dati trattati in filiale, inclusi dati di performance del team',
                'retention_period' => '10 anni, o per tutta la durata in caso di contenzioso/reclamo.',
                'extra_eu_transfer' => 'No',
                'security_measures' => 'Audit log, autorizzazione per accessi di livello superiore, gestione ruoli e permessi fisici/logici.'
            ]
        ];

        foreach ($ruoliPrivacy as $nomeRuolo => $datiPrivacy) {
            // updateOrCreate cerca per il primo array (chiave di ricerca)
            // e aggiorna/crea con il secondo array (i dati da inserire)
            \App\Models\EmploymentType::updateOrCreate(
                ['name' => $nomeRuolo],
                $datiPrivacy
            );
        }

        // Enasarco Limits
        \App\Models\EnasarcoLimit::firstOrCreate(['year' => 2024], ['name' => 'Massimali 2024', 'minimal_amount' => 1000, 'maximal_amount' => 45000]);
        \App\Models\EnasarcoLimit::firstOrCreate(['year' => 2025], ['name' => 'Massimali 2025', 'minimal_amount' => 1050, 'maximal_amount' => 46500]);
        \App\Models\EnasarcoLimit::firstOrCreate(['year' => 2026], ['name' => 'Massimali 2025', 'minimal_amount' => 1050, 'maximal_amount' => 46500]);

        // Practice Scopes
        $practiceScopes = [
            ['name' => 'Mutui', 'code' => 'MUT', 'oam_code' => 'A.1'],
            ['name' => 'Cessioni del V dello stipendio', 'code' => 'CessioneCQS', 'oam_code' => 'A.2'],
            ['name' => 'Cessioni del V pensione', 'code' => 'CessioneCQP', 'oam_code' => 'A.2'],
            ['name' => 'Delegazioni di pagamento', 'code' => 'Delega', 'oam_code' => 'A.2'],
            ['name' => 'Factoring crediti', 'code' => 'FACT', 'oam_code' => 'A.3'],
            ['name' => 'Acquisto di crediti', 'code' => 'ACQ_CRED', 'oam_code' => 'A.4'],
            ['name' => 'Leasing autoveicoli e aeronavali', 'code' => 'LEASE_AUTO', 'oam_code' => 'A.5'],
            ['name' => 'Leasing immobiliare', 'code' => 'LEASE_IMMO', 'oam_code' => 'A.6'],
            ['name' => 'Leasing strumentale', 'code' => 'LEASE_STRUM', 'oam_code' => 'A.7'],
            ['name' => 'Leasing su fonti rinnovabili ed altre tipologie di investimento', 'code' => 'LEASE_RINN', 'oam_code' => 'A.8'],
            ['name' => 'Aperture di credito in conto corrente', 'code' => 'APERT_CCC', 'oam_code' => 'A.9'],
            ['name' => 'Credito personale', 'code' => 'CRED_PERS', 'oam_code' => 'A.10'],
            ['name' => 'Credito finalizzato', 'code' => 'CRED_FIN', 'oam_code' => 'A.11'],
            ['name' => 'Prestito su pegno', 'code' => 'PREST_PEGNO', 'oam_code' => 'A.12'],
            ['name' => 'Rilascio di fidejussioni e garanzie', 'code' => 'FIDEJUSSIONI', 'oam_code' => 'A.13'],
            ['name' => 'Garanzia collettiva dei fidi', 'code' => 'GAR_COLLETTIVA', 'oam_code' => 'A.13-bis'],
            ['name' => 'Anticipi e sconti commerciali', 'code' => 'ANT_SCONTI', 'oam_code' => 'A.14'],
            ['name' => 'Credito revolving', 'code' => 'CRED_REV', 'oam_code' => 'A.15'],
            ['name' => 'Ristrutturazione dei crediti (art. 128-quater decies, del TUB)', 'code' => 'RISTRUTT_CRED', 'oam_code' => 'A.16'],
        ];
        foreach ($practiceScopes as $ps) {
            \App\Models\PracticeScope::firstOrCreate(['name' => $ps['name']], $ps);
        }

        // Software Categories
        $softwareCats = [
            ['name' => 'CRM', 'code' => 'CRM', 'description' => 'Customer Relationship Management'],
            ['name' => 'Contabilità', 'code' => 'COG', 'description' => 'Sistemi Contabili'],
            ['name' => 'Firma Elettronica', 'code' => 'SIGN', 'description' => 'Servizi di Firma Digitale'],
            ['name' => 'Documentale', 'code' => 'DOC', 'description' => 'Conservazione Documentale'],
            ['name' => 'Call Center', 'code' => 'CAL', 'description' => 'Call Center'],
            ['name' => 'Creditizie', 'code' => 'SIC', 'description' => 'Informazioni creditizie'],
        ];
        foreach ($softwareCats as $cat) {
            \App\Models\SoftwareCategory::firstOrCreate(['code' => $cat['code']], $cat);
        }

        $crmCat = \App\Models\SoftwareCategory::where('code', 'CRM')->first();

        $crmSIC = \App\Models\SoftwareCategory::where('code', 'SIC')->first();

        $crmCOG = \App\Models\SoftwareCategory::where('code', 'COG')->first();

        $crmSIGN = \App\Models\SoftwareCategory::where('code', 'SIGN')->first();

        $crmDOC = \App\Models\SoftwareCategory::where('code', 'DOC')->first();

        $crmCAL = \App\Models\SoftwareCategory::where('code', 'CAL')->first();

        if ($crmCat) {
            $crms = [
                [
                    'name' => 'MediaFacile',
                    'provider' => 'Moggio.',
                    'description' => 'Piattaforma specializzata per intermediazione creditizia.'
                ],
                [
                    'name' => 'Sifarma (Eurosystem)',
                    'provider' => 'Eurosystem S.p.A.',
                    'description' => 'Standard di mercato per la Cessione del Quinto.'
                ],
                [
                    'name' => 'Piteco (Finance)',
                    'provider' => 'Piteco S.p.A.',
                    'description' => 'Gestione tesoreria e flussi finanziari complessi.'
                ],
                [
                    'name' => 'Aliante (TeamSystem)',
                    'provider' => 'TeamSystem S.p.A.',
                    'description' => 'Specializzato per mediatori creditizi e agenti OAM.'
                ],
                [
                    'name' => 'MyCreditManager',
                    'provider' => 'Effidit S.r.l.',
                    'description' => "Piattaforma cloud per l'intermediazione creditizia."
                ],
                [
                    'name' => 'Kiron Open',
                    'provider' => 'Kiron Partner S.p.A.',
                    'description' => 'Software proprietario per reti di mediazione.'
                ],
                [
                    'name' => 'HubSpot',
                    'provider' => 'HubSpot Inc.',
                    'description' => 'CRM Cloud per inbound marketing e lead generation.'
                ],
                [
                    'name' => 'Salesforce Financial Services',
                    'provider' => 'Salesforce Inc.',
                    'description' => 'Verticale bancario e finanziario altamente personalizzabile.'
                ]
            ];
            foreach ($crms as $crm) {
                \App\Models\SoftwareApplication::firstOrCreate(
                    ['name' => $crm['name']],
                    [
                        'category_id' => $crmCat->id,
                        'provider_name' => $crm['provider'],
                        'is_cloud' => 1  // Ormai quasi tutti i player OAM sono passati al cloud/SaaS
                    ]
                );
            }
            if ($crmSIC) {
                $sics = [
                    [
                        'name' => 'CRIF (Mister Credit/Eurisc)',
                        'provider' => 'CRIF S.p.A.',
                        'description' => 'Il principale SIC in Italia per lo storico creditizio.'
                    ],
                    [
                        'name' => 'Experian',
                        'provider' => 'Experian Italia S.p.A.',
                        'description' => 'Centrale rischi privata utilizzata per il credit scoring.'
                    ],
                    [
                        'name' => 'Cerved Group',
                        'provider' => 'Cerved Group S.p.A.',
                        'description' => 'Analisi affidabilità imprese e visure camerali.'
                    ],
                    [
                        'name' => 'Banca Italia (Centrale Rischi)',
                        'provider' => "Banca d'Italia",
                        'description' => 'Accesso ai dati della Centrale Rischi pubblica.'
                    ],
                    [
                        'name' => 'Pitney Bowes (Confirm)',
                        'provider' => 'Pitney Bowes',
                        'description' => "Software per la verifica dell'identità e KYC."
                    ]
                ];

                foreach ($sics as $sic) {
                    \App\Models\SoftwareApplication::firstOrCreate(
                        ['name' => $sic['name']],
                        [
                            'category_id' => $crmSIC->id,
                            'provider_name' => $sic['provider'],
                            'is_cloud' => 1  // Ormai quasi tutti i player OAM sono passati al cloud/SaaS
                        ]
                    );
                }
            }
            if ($crmCAL) {
                $callCenterSoftwares = [
                    [
                        'name' => 'Sidial',
                        'provider' => 'Sidial S.r.l.',
                        'description' => 'Leader in Italia per il telemarketing e la gestione liste CQS.'
                    ],
                    [
                        'name' => 'ViciDial',
                        'provider' => 'Open Source Project',
                        'description' => 'Il dialer predittivo open source più utilizzato al mondo.'
                    ],
                    [
                        'name' => 'XCALLY',
                        'provider' => 'Xenialab S.r.l. (Injenia)',
                        'description' => 'Soluzione omnicanale avanzata per customer care e outbound.'
                    ],
                    [
                        'name' => 'Genesis Cloud',
                        'provider' => 'Genesys',
                        'description' => 'Piattaforma enterprise per grandi call center finanziari.'
                    ],
                    [
                        'name' => 'NICE CXone',
                        'provider' => 'NICE Systems',
                        'description' => 'Analisi avanzata e gestione operativa per contact center.'
                    ],
                    [
                        'name' => '3CX',
                        'provider' => '3CX Ltd',
                        'description' => 'Centralino VoIP integrato con i principali CRM.'
                    ]
                ];

                foreach ($callCenterSoftwares as $software) {
                    \App\Models\SoftwareApplication::firstOrCreate(
                        ['name' => $software['name']],
                        [
                            'category_id' => $crmCAL->id,
                            'provider_name' => $software['provider'],
                            'is_cloud' => 1
                        ]
                    );
                }
            }
            if ($crmDOC) {
                $docSoftwares = [
                    [
                        'name' => 'InfoCert (Legalinvoice)',
                        'provider' => 'InfoCert S.p.A. (Tinexta Group)',
                        'description' => 'Leader europeo per la conservazione digitale e firma elettronica qualificata.'
                    ],
                    [
                        'name' => 'Namirial Compliance',
                        'provider' => 'Namirial S.p.A.',
                        'description' => 'Soluzioni integrate per firma grafometrica e conservazione a norma.'
                    ],
                    [
                        'name' => 'DocFly',
                        'provider' => 'Aruba S.p.A.',
                        'description' => 'Servizi di conservazione massiva e certificazione dei documenti.'
                    ],
                    [
                        'name' => 'Zucchetti Infinity DMS',
                        'provider' => 'Zucchetti S.p.A.',
                        'description' => 'Gestione documentale avanzata integrabile con i sistemi contabili.'
                    ],
                    [
                        'name' => 'CompEd Service',
                        'provider' => 'CompEd S.r.l.',
                        'description' => 'Specializzati in soluzioni verticali per la normativa italiana sulla conservazione.'
                    ],
                    [
                        'name' => 'Archiflow',
                        'provider' => 'Siav S.p.A.',
                        'description' => "Piattaforma di Content Services per l'automazione dei flussi documentali."
                    ]
                ];

                foreach ($docSoftwares as $software) {
                    \App\Models\SoftwareApplication::firstOrCreate(
                        ['name' => $software['name']],
                        [
                            'category_id' => $crmDOC->id,
                            'provider_name' => $software['provider'],
                            'is_cloud' => 1
                        ]
                    );
                }
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
}
