CDROP TABLE IF EXISTS `abis`;
CREATE TABLE `abis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco interno',
  `abi` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice ABI a 5 cifre',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome ufficiale (es. AGOS DUCATO S.P.A.)',
  `type` enum('BANCA','INTERMEDIARIO_106','IP_IMEL') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Banca o Finanziaria ex Art. 106 TUB',
  `capogruppo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Gruppo bancario di appartenenza',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPERATIVO' COMMENT 'OPERATIVO, CANCELLATO, IN_LIQUIDAZIONE',
  `data_iscrizione` date DEFAULT NULL,
  `data_cancellazione` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Data creazione record',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Data ultimo aggiornamento',
  PRIMARY KEY (`id`),
  UNIQUE KEY `financials_abi_code_unique` (`abi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `address_types`;
CREATE TABLE `address_types` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID univoco tipo indirizzo',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `addressable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Classe del Modello collegato (es. App\\Models\\Client)',
  `addressable_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Via e numero civico',
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Città o Comune',
  `zip_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CAP (Codice di Avviamento Postale)',
  `address_type_id` int DEFAULT NULL COMMENT 'Relazione con tipologia indirizzo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data inserimento indirizzo',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento',
  PRIMARY KEY (`id`),
  KEY `address_type_id` (`address_type_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`address_type_id`) REFERENCES `address_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella polimorfica per salvare molteplici indirizzi associabili a Company, Clienti o Utenti.';


DROP TABLE IF EXISTS `agents`;
CREATE TABLE `agents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco agente',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''istituto bancario o finanziaria (es. Intesa Sanpaolo, Compass)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `oam` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Oam',
  `oam_at` date DEFAULT NULL COMMENT 'Data iscrizione OAM',
  `oam_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Denominazione sociale registrata in OAM',
  `stipulated_at` date DEFAULT NULL COMMENT 'Data stipula contratto collaborazione',
  `dismissed_at` date DEFAULT NULL COMMENT 'Data cessazione rapporto',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Agente / Mediatore / Consulente / Call Center ',
  `contribute` decimal(10,2) DEFAULT NULL COMMENT 'Importo contributo fisso/quota',
  `contributeFrequency` int DEFAULT '1' COMMENT 'Frequenza contributo (mesi)',
  `contributeFrom` date DEFAULT NULL COMMENT 'Data inizio addebito contributi',
  `remburse` decimal(10,2) DEFAULT NULL COMMENT 'Importo rimborsi spese concordati',
  `vat_number` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Partita IVA Agente',
  `vat_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ragione Sociale Fiscale',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica se la banca è attualmente convenzionata',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant di appartenenza',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `agents_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella globale agenti convenzionati.';


DROP TABLE IF EXISTS `api_configurations`;
CREATE TABLE `api_configurations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco configurazione',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant proprietario della connessione',
  `software_application_id` int unsigned NOT NULL COMMENT 'Software con cui interfacciarsi',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome mnemonico della connessione',
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL base dell''API (es. https://api.crmesterno.it/v1)',
  `auth_type` enum('BASIC','BEARER_TOKEN','API_KEY','OAUTH2') COLLATE utf8mb4_unicode_ci DEFAULT 'API_KEY' COMMENT 'Metodo di autenticazione',
  `api_key` text COLLATE utf8mb4_unicode_ci COMMENT 'Chiave API o Client ID',
  `api_secret` text COLLATE utf8mb4_unicode_ci COMMENT 'Segreto API o Client Secret',
  `access_token` text COLLATE utf8mb4_unicode_ci COMMENT 'Token di accesso attuale (se OAUTH2 o BEARER)',
  `refresh_token` text COLLATE utf8mb4_unicode_ci COMMENT 'Token per il rinnovo della sessione',
  `token_expires_at` timestamp NULL DEFAULT NULL COMMENT 'Data di scadenza del token attuale',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Indica se l''integrazione è abilitata',
  `webhook_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chiave per validare i dati in entrata (Webhooks)',
  `last_sync_at` timestamp NULL DEFAULT NULL COMMENT 'Data e ora dell''ultima sincronizzazione riuscita',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data creazione configurazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento configurazione',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `software_application_id` (`software_application_id`),
  CONSTRAINT `api_configurations_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `api_configurations_ibfk_2` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurazioni tecniche per l''interfacciamento API con software terzi.';


DROP TABLE IF EXISTS `api_logs`;
CREATE TABLE `api_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_configuration_id` int unsigned NOT NULL COMMENT 'Riferimento alla configurazione usata',
  `api_loggable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe del Modello collegato',
  `api_loggable_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID del Modello (VARCHAR 36)',
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'L''endpoint specifico chiamato',
  `method` enum('GET','POST','PUT','DELETE','PATCH') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `request_payload` json DEFAULT NULL COMMENT 'Dati inviati',
  `response_payload` json DEFAULT NULL COMMENT 'Dati ricevuti',
  `status_code` int DEFAULT NULL COMMENT 'Codice HTTP (es. 200, 401, 500)',
  `execution_time_ms` int DEFAULT NULL COMMENT 'Tempo di risposta in millisecondi',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione dell''errore se fallito',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e ora della chiamata',
  PRIMARY KEY (`id`),
  KEY `api_configuration_id` (`api_configuration_id`),
  CONSTRAINT `api_logs_ibfk_1` FOREIGN KEY (`api_configuration_id`) REFERENCES `api_configurations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro storico di tutte le chiamate API effettuate per monitoraggio e risoluzione problemi.';


DROP TABLE IF EXISTS `audit_items`;
CREATE TABLE `audit_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID singola riga di controllo',
  `audit_id` int unsigned NOT NULL COMMENT 'Riferimento alla sessione di audit',
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Classe dell''oggetto controllato (es. App\\Models\\Practice)',
  `auditable_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID dell''oggetto controllato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `result` enum('OK','RILIEVO','GRAVE_INADEMPIENZA','NON_CONTROLLATO') COLLATE utf8mb4_unicode_ci DEFAULT 'OK',
  `finding_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione dell''eventuale anomalia riscontrata',
  `remediation_plan` text COLLATE utf8mb4_unicode_ci COMMENT 'Azioni correttive richieste per sanare l''anomalia',
  `remediation_deadline` date DEFAULT NULL COMMENT 'Scadenza entro cui sanare il rilievo',
  `is_resolved` tinyint(1) DEFAULT '0' COMMENT 'Indica se il rilievo è stato chiuso con successo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_id` (`audit_id`),
  CONSTRAINT `audit_items_ibfk_1` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Singole verifiche effettuate durante un audit su specifiche pratiche o fascicoli agenti.';


DROP TABLE IF EXISTS `audits`;
CREATE TABLE `audits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco audit',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant oggetto del controllo',
  `requester_type` enum('OAM','PRINCIPAL','INTERNAL','EXTERNAL') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chi richiede l''audit: Ente Regolatore, Mandante o Auto-controllo interno',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Se requester è PRINCIPAL, indicare quale',
  `agent_id` int unsigned DEFAULT NULL COMMENT 'Agente specifico oggetto di audit (se applicabile)',
  `regulatory_body_id` int unsigned DEFAULT NULL COMMENT 'Ente regolatore che richiede l''audit (se applicabile)',
  `client_id` int unsigned DEFAULT NULL COMMENT 'Cliente specifico oggetto di audit (se applicabile)',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Titolo dell''ispezione (es. Audit Semestrale Trasparenza 2026)',
  `emails` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Lista email per notifiche esiti audit',
  `reference_period` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Periodo oggetto di analisi (es. Q1-Q2 2025)',
  `start_date` date NOT NULL COMMENT 'Data inizio ispezione',
  `end_date` date DEFAULT NULL COMMENT 'Data chiusura ispezione',
  `status` enum('PROGRAMMATO','IN_CORSO','COMPLETATO','ARCHIVIATO') COLLATE utf8mb4_unicode_ci DEFAULT 'PROGRAMMATO',
  `overall_score` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Valutazione sintetica finale (es. Conforme, Conforme con rilievi, Non Conforme)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `principal_id` (`principal_id`),
  KEY `agent_id` (`agent_id`),
  KEY `regulatory_body_id` (`regulatory_body_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `audits_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audits_ibfk_2` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audits_ibfk_3` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`),
  CONSTRAINT `audits_ibfk_4` FOREIGN KEY (`regulatory_body_id`) REFERENCES `regulatory_bodies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audits_ibfk_5` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sessioni di Audit richieste da OAM, Mandanti o effettuate internamente.';


DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `client_practice`;
CREATE TABLE `client_practice` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del legame',
  `practice_id` int unsigned NOT NULL COMMENT 'Riferimento alla pratica',
  `client_id` int unsigned NOT NULL COMMENT 'Riferimento al cliente coinvolto',
  `role` enum('intestatario','cointestatario','garante','terzo_datore') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'intestatario' COMMENT 'Ruolo legale del cliente nella pratica: Intestatario principale, Co-intestatario, Garante o Terzo Datore di ipoteca',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tenant di riferimento del legame',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di associazione del cliente alla pratica',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_client_practice` (`practice_id`,`client_id`),
  KEY `client_id` (`client_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `client_practice_ibfk_1` FOREIGN KEY (`practice_id`) REFERENCES `practices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_practice_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_practice_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di legame tra Clienti e Pratiche. Gestisce chi sono gli intestatari e chi i garanti per ogni pratica.';


DROP TABLE IF EXISTS `client_privacies`;
CREATE TABLE `client_privacies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tentant ID',
  `client_id` int unsigned NOT NULL COMMENT 'Riferimento al cliente',
  `request_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Accesso, Rettifica, Cancellazione, Portabilità',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ricevuta, In lavorazione, Evasa',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Data della risposta definitiva',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_privacies_company_id_index` (`company_id`),
  KEY `client_privacies_client_id_index` (`client_id`),
  CONSTRAINT `client_privacies_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_privacies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `client_types`;
CREATE TABLE `client_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco tipo cliente',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalogo globale: Classificazione lavorativa del cliente (fondamentale per le logiche di delibera del credito).';


DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Vincolo multi-tenant: l''agenzia proprietaria del dato',
  `is_person` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Persona fisica (true) o giuridica (false)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome (se persona fisica) o Ragione Sociale (se giuridica)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome persona fisica',
  `tax_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice Fiscale o Partita IVA del cliente',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email di contatto principale',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Recapito telefonico',
  `is_pep` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Persona Politicamente Esposta',
  `client_type_id` int unsigned DEFAULT NULL COMMENT 'Classificazione cliente',
  `is_sanctioned` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Presente in liste antiterrorismo/blacklists',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data acquisizione cliente',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica anagrafica',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `client_type_id` (`client_type_id`),
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`client_type_id`) REFERENCES `client_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clienti (Richiedenti credito) associati in modo esclusivo a una specifica agenzia (Tenant).';


DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID v4 generato da Laravel (Chiave Primaria)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ragione Sociale della società di mediazione',
  `vat_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Partita IVA o Codice Fiscale dell''agenzia',
  `vat_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Denominazione fiscale per fatturazione',
  `oam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero iscrizione OAM Società',
  `oam_at` date DEFAULT NULL COMMENT 'Data iscrizione OAM Società',
  `oam_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome registrato negli elenchi OAM',
  `company_type_id` int unsigned DEFAULT NULL COMMENT 'Tipo forma giuridica della società',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione del tenant',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data di ultima modifica',
  PRIMARY KEY (`id`),
  KEY `companies_company_type_id_foreign` (`company_type_id`),
  CONSTRAINT `companies_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella principale dei Tenant (Società di Mediazione Creditizia).';


DROP TABLE IF EXISTS `company_branches`;
CREATE TABLE `company_branches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco filiale',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant proprietario della sede (UUID)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome della sede (es. Sede Centrale, Filiale Milano Nord)',
  `is_main_office` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se questa è la sede legale/principale dell''agenzia',
  `manager_first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome del referente/responsabile della sede',
  `manager_last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cognome del referente/responsabile della sede',
  `manager_tax_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice Fiscale del referente della sede',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data creazione sede',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento sede',
  PRIMARY KEY (`id`),
  KEY `idx_company_main` (`company_id`,`is_main_office`),
  CONSTRAINT `company_branches_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anagrafica delle sedi operative e legali delle società di mediazione con relativi referenti.';

INSERT INTO `company_branches` (`id`, `company_id`, `name`, `is_main_office`, `manager_first_name`, `manager_last_name`, `manager_tax_code`, `created_at`, `updated_at`) VALUES
(1,	'f5742777-1669-4353-a78c-3370815837b7',	'Sede Legale Milano',	1,	'Mario',	'Rossi',	NULL,	'2026-02-22 17:51:35',	'2026-02-22 17:51:35');

DROP TABLE IF EXISTS `company_software_application`;
CREATE TABLE `company_software_application` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID dell''azienda',
  `software_application_id` int unsigned NOT NULL COMMENT 'ID del software',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ATTIVO' COMMENT 'Stato dell''associazione (es. ATTIVO, SOSPESO)',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note specifiche per l''azienda',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_company_software` (`company_id`,`software_application_id`),
  KEY `company_software_application_software_application_id_foreign` (`software_application_id`),
  CONSTRAINT `company_software_application_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_software_application_software_application_id_foreign` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `company_types`;
CREATE TABLE `company_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es. S.p.A., S.r.l., Ditta Individuale',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Forme giuridiche delle società.';

INSERT INTO `company_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1,	'Mediatore',	'2026-02-22 17:51:34',	'2026-02-22 17:51:34'),
(2,	'Call Center',	'2026-02-22 17:51:34',	'2026-02-22 17:51:34'),
(3,	'Albergo',	'2026-02-22 17:51:34',	'2026-02-22 17:51:34'),
(4,	'Software House',	'2026-02-22 17:51:34',	'2026-02-22 17:51:34');

DROP TABLE IF EXISTS `company_websites`;
CREATE TABLE `company_websites` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del sito',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant proprietario del sito',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome del sito (es. Portale Agenti Roma)',
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dominio o sottodominio (es. agenzia-x.mediaconsulence.it)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipologia sito (Vetrina, Portale, Landing)',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Mandante di riferimento per landing dedicate',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Stato del sito (online/offline)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`),
  KEY `company_id` (`company_id`),
  KEY `principal_id` (`principal_id`),
  CONSTRAINT `company_websites_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_websites_ibfk_2` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurazioni dei siti web e portali personalizzati per ogni agenzia.';

INSERT INTO `company_websites` (`id`, `company_id`, `name`, `domain`, `type`, `principal_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1,	'f5742777-1669-4353-a78c-3370815837b7',	'Portale Principale',	'www.mainagency.it',	'Vetrina',	NULL,	1,	'2026-02-22 17:51:35',	'2026-02-22 17:51:35');

DROP TABLE IF EXISTS `comunes`;
CREATE TABLE `comunes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codice_regione` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_unita_territoriale` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_provincia_storico` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `progressivo_comune` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_alfanumerico` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_italiano` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_altra_lingua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codice_ripartizione_geografica` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ripartizione_geografica` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_regione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_unita_territoriale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipologia_unita_territoriale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capoluogo_provincia` tinyint(1) NOT NULL DEFAULT '0',
  `sigla_automobilistica` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_numerico` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_110_province` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_107_province` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_103_province` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_catastale` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts1_2021` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts2_2021` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts3_2021` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts1_2024` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts2_2024` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts3_2024` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comunes_codice_regione_index` (`codice_regione`),
  KEY `comunes_codice_provincia_storico_index` (`codice_provincia_storico`),
  KEY `comunes_denominazione_regione_index` (`denominazione_regione`),
  KEY `comunes_sigla_automobilistica_index` (`sigla_automobilistica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `document_scopes`;
CREATE TABLE `document_scopes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco ambito',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''ambito: Privacy, AML, OAM, Istruttoria, Contrattualistica',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione della finalità normativa',
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#6B7280' COMMENT 'Codice colore per i tag nell''interfaccia (Filament Badge)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale: Definisce le finalità normative dei documenti.';


DROP TABLE IF EXISTS `document_type_scope`;
CREATE TABLE `document_type_scope` (
  `document_type_id` int unsigned NOT NULL COMMENT 'ID tipo documento',
  `document_scope_id` int unsigned NOT NULL COMMENT 'ID ambito normativo',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  PRIMARY KEY (`document_type_id`,`document_scope_id`),
  KEY `document_scope_id` (`document_scope_id`),
  CONSTRAINT `document_type_scope_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_type_scope_ibfk_2` FOREIGN KEY (`document_scope_id`) REFERENCES `document_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella pivot per associare uno o più ambiti (tag) a ogni tipologia di documento.';


DROP TABLE IF EXISTS `document_types`;
CREATE TABLE `document_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di inserimento a sistema',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Tipologie di documenti riconosciuti per l''Adeguata Verifica.';


DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `practice_id` int unsigned NOT NULL,
  `document_type_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'uploaded',
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_company_id_foreign` (`company_id`),
  KEY `documents_practice_id_index` (`practice_id`),
  KEY `documents_document_type_id_index` (`document_type_id`),
  CONSTRAINT `documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_practice_id_foreign` FOREIGN KEY (`practice_id`) REFERENCES `practices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco dipendente',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Agenzia di appartenenza',
  `user_id` int unsigned DEFAULT NULL COMMENT 'Legame con l''utente di sistema',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome completo dipendente',
  `role_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Qualifica aziendale (es. Responsabile Backoffice)',
  `cf` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice Fiscale',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email aziendale dipendente',
  `phone` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefono o interno dipendente',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dipartimento (es. Amministrazione, Compliance)',
  `oam` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice OAM individuale dipendente',
  `ivass` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice IVASS individuale dipendente',
  `hiring_date` date DEFAULT NULL COMMENT 'Data di assunzione',
  `termination_date` date DEFAULT NULL COMMENT 'Data di fine rapporto',
  `company_branch_id` int unsigned DEFAULT NULL COMMENT 'Sede fisica di assegnazione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `company_branch_id` (`company_branch_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`company_branch_id`) REFERENCES `company_branches` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anagrafica dipendenti interni delle società di mediazione.';


DROP TABLE IF EXISTS `employment_types`;
CREATE TABLE `employment_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalogo globale: Classificazione lavorativa del cliente (fondamentale per le logiche di delibera del credito).';


DROP TABLE IF EXISTS `enasarco_limits`;
CREATE TABLE `enasarco_limits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `year` int NOT NULL COMMENT 'Anno di riferimento per l''aliquota',
  `minimal_amount` decimal(10,2) NOT NULL COMMENT 'Minimale contributivo annuo in Euro',
  `maximal_amount` decimal(10,2) NOT NULL COMMENT 'Massimale provvigionale annuo in Euro',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data inserimento record',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data aggiornamento importi',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Massimali e minimali annui stabiliti dalla Fondazione Enasarco.';


DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `oam_scopes`;
CREATE TABLE `oam_scopes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID autoincrementante',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice ambito OAM',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione ambito operativo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oam_scopes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Ambiti operativi OAM.';


DROP TABLE IF EXISTS `oams`;
CREATE TABLE `oams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autorizzato_ad_operare` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `persona` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_fiscale` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicilio_sede_legale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elenco` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_iscrizione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_iscrizione` date DEFAULT NULL,
  `stato` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_stato` date DEFAULT NULL,
  `causale_stato_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oams_codice_fiscale_unique` (`codice_fiscale`),
  KEY `oams_codice_fiscale_index` (`codice_fiscale`),
  KEY `oams_elenco_index` (`elenco`),
  KEY `oams_stato_index` (`stato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `practice_commissions`;
CREATE TABLE `practice_commissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant dell''agenzia',
  `practice_id` int unsigned NOT NULL COMMENT 'La pratica che ha generato la provvigione',
  `proforma_id` int unsigned DEFAULT NULL COMMENT 'Il proforma in cui questa provvigione è stata liquidata (NULL se non ancora liquidata)',
  `agent_id` int unsigned DEFAULT NULL COMMENT 'L''agente beneficiario',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Mandante',
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'Importo provvigionale lordo per questa singola pratica',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dettaglio (es. Bonus extra o Provvigione base)',
  `perfected_at` date DEFAULT NULL,
  `is_coordination` tinyint(1) DEFAULT NULL COMMENT 'Compenso coordinamento',
  `cancellation_at` date DEFAULT NULL,
  `invoice_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_at` date DEFAULT NULL,
  `paided_at` date DEFAULT NULL,
  `is_storno` tinyint(1) DEFAULT NULL,
  `is_enasarco` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `practice_id` (`practice_id`),
  KEY `proforma_id` (`proforma_id`),
  KEY `agent_id` (`agent_id`),
  CONSTRAINT `practice_commissions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practice_commissions_ibfk_2` FOREIGN KEY (`practice_id`) REFERENCES `practices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practice_commissions_ibfk_3` FOREIGN KEY (`proforma_id`) REFERENCES `proformas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practice_commissions_ibfk_4` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Singole righe provvigionali maturate dalle pratiche. Vengono raggruppate nel proforma mensile.';


DROP TABLE IF EXISTS `practice_scopes`;
CREATE TABLE `practice_scopes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es. Mutui Ipotecari, Cessioni del Quinto, Prestiti Personali',
  `oam_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella tipologia finanziamento';


DROP TABLE IF EXISTS `practice_status_lookup`;
CREATE TABLE `practice_status_lookup` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dello stato (es. istruttoria, deliberata, erogata)',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Colore del badge per Filament (es. warning, success, danger)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione dettagliata dello stato',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Se lo stato è utilizzabile',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Ordinamento visualizzazione',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella lookup per gli stati delle pratiche con colori associati';


DROP TABLE IF EXISTS `practice_statuses`;
CREATE TABLE `practice_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `practice_id` int unsigned NOT NULL COMMENT 'La pratica a cui si riferisce il cambio di stato',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Valore dello stato (es. istruttoria, delibera, erogata, annullata)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Eventuali motivazioni o note interne (es. motivo del respinto)',
  `changed_by` int unsigned NOT NULL COMMENT 'L''utente (backoffice/admin) che ha aggiornato lo stato',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e ora esatta del cambio stato (Audit Log)',
  PRIMARY KEY (`id`),
  KEY `practice_id` (`practice_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `practice_statuses_ibfk_1` FOREIGN KEY (`practice_id`) REFERENCES `practices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practice_statuses_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Storico cronologico dei cambi di stato della pratica per monitorare i tempi di lavorazione (KPI).';


DROP TABLE IF EXISTS `practices`;
CREATE TABLE `practices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante della pratica',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Vincolo multi-tenant: l''agenzia che gestisce la pratica',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Mandante (banca)',
  `bank_id` int unsigned DEFAULT NULL COMMENT 'Banca erogante',
  `agent_id` int unsigned DEFAULT NULL COMMENT 'Agente o collaboratore per provvigioni',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice o nome identificativo (es. Mutuo Acquisto Prima Casa Rossi)',
  `CRM_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice CRM interno',
  `principal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice mandante',
  `amount` decimal(12,2) DEFAULT NULL COMMENT 'Importo del finanziamento/mutuo richiesto o erogato',
  `net` decimal(12,2) DEFAULT NULL COMMENT 'Netto erogato',
  `practice_scope_id` int unsigned DEFAULT NULL COMMENT 'Ambito della pratica',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'istruttoria' COMMENT 'Stato: istruttoria, deliberata, erogata, respinta',
  `perfected_at` date DEFAULT NULL COMMENT 'Data perfezionamento pratica',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Pratica attiva/inattiva',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data caricamento pratica',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo cambio stato',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `principal_id` (`principal_id`),
  KEY `agent_id` (`agent_id`),
  KEY `practice_scope_id` (`practice_scope_id`),
  CONSTRAINT `practices_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practices_ibfk_3` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practices_ibfk_4` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`),
  CONSTRAINT `practices_ibfk_5` FOREIGN KEY (`practice_scope_id`) REFERENCES `practice_scopes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pratiche di mediazione (Mutui, Cessioni, Prestiti personali) caricate a sistema.';


DROP TABLE IF EXISTS `principal_contacts`;
CREATE TABLE `principal_contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco contatto mandante',
  `principal_id` int unsigned NOT NULL COMMENT 'Riferimento alla banca mandante',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome del referente bancario',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome del referente bancario',
  `role_title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo (es. Responsabile Istruttoria, Area Manager, Deliberante)',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dipartimento (es. Ufficio Mutui, Compliance, Estero)',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email diretta del referente',
  `phone_office` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefono ufficio / interno',
  `phone_mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cellulare aziendale',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Indica se il referente è ancora il punto di contatto',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note utili (es. "Contattare solo per pratiche sopra i 200k")',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_principal_contact_search` (`last_name`,`department`),
  KEY `principal_id` (`principal_id`),
  CONSTRAINT `principal_contacts_ibfk_1` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rubrica dei referenti presso le banche mandanti per comunicazioni operative e istruttoria.';


DROP TABLE IF EXISTS `principal_mandates`;
CREATE TABLE `principal_mandates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del mandato',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant (Mediatore) titolare del mandato',
  `principal_id` int unsigned NOT NULL COMMENT 'Banca o Istituto mandante',
  `mandate_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numero di protocollo o identificativo del contratto di mandato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `start_date` date NOT NULL COMMENT 'Data di decorrenza del mandato',
  `end_date` date DEFAULT NULL COMMENT 'Data di scadenza (NULL se a tempo indeterminato)',
  `is_exclusive` tinyint(1) DEFAULT '0' COMMENT 'Indica se il mandato prevede l''esclusiva per quella categoria',
  `status` enum('ATTIVO','SCADUTO','RECEDUTO','SOPESO') COLLATE utf8mb4_unicode_ci DEFAULT 'ATTIVO' COMMENT 'Stato operativo del mandato',
  `contract_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Riferimento al PDF del contratto firmato',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note su provvigioni particolari o patti specifici',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `principal_id` (`principal_id`),
  CONSTRAINT `principal_mandates_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `principal_mandates_ibfk_2` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contratti di mandato che legano l''agenzia agli Istituti Bancari.';


DROP TABLE IF EXISTS `principal_scopes`;
CREATE TABLE `principal_scopes` (
  `principal_id` int unsigned NOT NULL COMMENT 'Riferimento al mandato',
  `practice_scope_id` int unsigned NOT NULL COMMENT 'Riferimento all''ambito (es. Cessione del Quinto, Mutui)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `principal_id` (`principal_id`),
  KEY `practice_scope_id` (`practice_scope_id`),
  CONSTRAINT `principal_scopes_ibfk_2` FOREIGN KEY (`practice_scope_id`) REFERENCES `practice_scopes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `principal_scopes_ibfk_3` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella pivot: definisce quali comparti operativi sono autorizzati per ogni singolo mandato.';


DROP TABLE IF EXISTS `principals`;
CREATE TABLE `principals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''istituto bancario o finanziaria (es. Intesa Sanpaolo, Compass)',
  `abi` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Abi per banche o codice ISVASS',
  `stipulated_at` date DEFAULT NULL COMMENT 'Data stipula contratto convenzione',
  `dismissed_at` date DEFAULT NULL COMMENT 'Data cessazione rapporto convenzione',
  `vat_number` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Partita IVA dell''istituto',
  `vat_name` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ragione sociale fiscale',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Banca / Assicurazione / Utility',
  `oam` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice di iscrizione OAM',
  `ivass` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice di iscrizione IVASS',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica se la banca è attualmente convenzionata',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant di appartenenza',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mandate_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Numero di protocollo o identificativo del contratto di mandato',
  `start_date` date NOT NULL COMMENT 'Data di decorrenza del mandato',
  `end_date` date DEFAULT NULL COMMENT 'Data di scadenza (NULL se a tempo indeterminato)',
  `is_exclusive` tinyint(1) DEFAULT '0' COMMENT 'Indica se il mandato prevede l''esclusiva per quella categoria',
  `status` enum('ATTIVO','SCADUTO','RECEDUTO','SOPESO') COLLATE utf8mb4_unicode_ci DEFAULT 'ATTIVO' COMMENT 'Stato operativo del mandato',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note su provvigioni particolari o patti specifici',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `principals_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella globale delle banche ed enti eroganti convenzionati.';


DROP TABLE IF EXISTS `proforma_status`;
CREATE TABLE `proforma_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `is_payable` tinyint(1) DEFAULT NULL,
  `is_external` tinyint(1) DEFAULT NULL,
  `is_ok` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `proforma_status_history`;
CREATE TABLE `proforma_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco log stato',
  `proforma_id` int unsigned NOT NULL COMMENT 'Riferimento al proforma',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lo stato assunto dal proforma',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `changed_by` int unsigned NOT NULL COMMENT 'L''utente (amministratore) che ha effettuato l''azione',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Eventuali note sul cambio stato (es. motivo dell''annullamento)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e ora esatta del passaggio di stato',
  PRIMARY KEY (`id`),
  KEY `proforma_id` (`proforma_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `proforma_status_history_ibfk_1` FOREIGN KEY (`proforma_id`) REFERENCES `proformas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proforma_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro storico dei passaggi di stato del proforma per controllo amministrativo.';


DROP TABLE IF EXISTS `proformas`;
CREATE TABLE `proformas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'L''agenzia che deve liquidare l''agente',
  `agent_id` int unsigned NOT NULL COMMENT 'L''agente beneficiario delle provvigioni',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Riferimento documento (es. Proforma 01/2026 - Rossi Mario)',
  `commission_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_commissions` decimal(10,2) DEFAULT NULL COMMENT 'Totale provvigioni lorde maturate nel periodo',
  `enasarco_retained` decimal(10,2) DEFAULT NULL COMMENT 'Quota Enasarco trattenuta dall''agenzia (50% del totale contributo)',
  `remburse` decimal(10,2) DEFAULT NULL,
  `remburse_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contribute` decimal(10,2) DEFAULT NULL,
  `contribute_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refuse` decimal(10,2) DEFAULT NULL,
  `refuse_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL COMMENT 'Importo netto da liquidare all''agente',
  `month` int DEFAULT NULL COMMENT 'Mese di competenza della liquidazione (1-12)',
  `year` int DEFAULT NULL COMMENT 'Anno di competenza',
  `status` enum('INSERITO','INVIATO','ANNULLATO','FATTURATO','PAGATO','STORICO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INSERITO',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di generazione del proforma',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica prima della fatturazione definitiva',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `agent_id` (`agent_id`),
  CONSTRAINT `proformas_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proformas_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proforma mensili generati dal sistema per calcolare compensi e ritenute Enasarco degli agenti.';


DROP TABLE IF EXISTS `regulatory_bodies`;
CREATE TABLE `regulatory_bodies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco dell''ente',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''ente (es. OAM - Organismo Agenti e Mediatori, Garante per la Protezione dei Dati Personali)',
  `acronym` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sigla (es. OAM, GPDP, IVASS)',
  `official_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sito web istituzionale',
  `pec_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Indirizzo PEC per comunicazioni legali',
  `portal_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL del portale riservato per invio flussi/segnalazioni',
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Eventuale referente o dirigente di riferimento',
  `phone_support` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero di telefono assistenza/ispettorato',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note su modalità di invio documenti o scadenze fisse',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anagrafica delle Autorità di Vigilanza e degli Enti preposti ai controlli normativi.';


DROP TABLE IF EXISTS `regulatory_body_scopes`;
CREATE TABLE `regulatory_body_scopes` (
  `regulatory_body_id` int unsigned NOT NULL COMMENT 'Riferimento all''ente',
  `document_scope_id` int unsigned NOT NULL COMMENT 'Riferimento all''ambito (es. Privacy, AML, OAM)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  PRIMARY KEY (`regulatory_body_id`,`document_scope_id`),
  KEY `document_scope_id` (`document_scope_id`),
  CONSTRAINT `regulatory_body_scope_ibfk_1` FOREIGN KEY (`regulatory_body_id`) REFERENCES `regulatory_bodies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `regulatory_body_scope_ibfk_2` FOREIGN KEY (`document_scope_id`) REFERENCES `document_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella pivot per definire quali ambiti normativi sono di competenza di ciascun ente.';


DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_company_id_name_guard_name_unique` (`company_id`,`name`,`guard_name`),
  CONSTRAINT `roles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `software_applications`;
CREATE TABLE `software_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco software',
  `category_id` int unsigned NOT NULL COMMENT 'Riferimento alla categoria',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome commerciale (es. Salesforce, XCrm, Teamsystem, Namirial)',
  `provider_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome della software house produttrice',
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sito web ufficiale del produttore',
  `api_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sandbox_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_parameters` text COLLATE utf8mb4_unicode_ci,
  `is_cloud` tinyint(1) DEFAULT '1' COMMENT 'Indica se il software è SaaS/Cloud o On-Premise',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `software_applications_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `software_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale: Elenco dei software più comuni nel settore finanziario.';


DROP TABLE IF EXISTS `software_categories`;
CREATE TABLE `software_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco categoria',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es. CRM, Call Center, Contabilità, AML, Firma Elettronica',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice tecnico (es. CRM, CALL_CENTER, ACC, AML)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione della tipologia di software',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale: Categorie di software utilizzati dalle agenzie.';


DROP TABLE IF EXISTS `software_mappings`;
CREATE TABLE `software_mappings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco mappatura',
  `software_application_id` int unsigned NOT NULL COMMENT 'Il software sorgente (es. CRM esterno)',
  `mapping_type` enum('PRACTICE_TYPE','PRACTICE_STATUS','CLIENT_TYPE','BANK_NAME') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cosa stiamo mappando (es. Tipo Pratica o Stato Pratica)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `external_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Il valore testuale nel CRM sorgente (es. "MUT_ACQ")',
  `internal_id` int unsigned NOT NULL COMMENT 'L''ID corrispondente nel nostro database (es. ID di "Mutuo Acquisto")',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mapping_lookup` (`software_application_id`,`mapping_type`,`external_value`),
  CONSTRAINT `software_mappings_ibfk_1` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle di conversione (Cross-Reference) per tradurre i dati da software esterni al formato interno.';


DROP TABLE IF EXISTS `training_records`;
CREATE TABLE `training_records` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID record partecipazione',
  `training_session_id` int unsigned NOT NULL COMMENT 'La sessione seguita',
  `employee_id` int unsigned DEFAULT NULL COMMENT 'Dipendente che ha partecipato alla formazione',
  `agent_id` int unsigned DEFAULT NULL COMMENT 'Agente che ha partecipato alla formazione',
  `status` enum('ISCRITTO','FREQUENTANTE','COMPLETATO','NON_SUPERATO') COLLATE utf8mb4_unicode_ci DEFAULT 'ISCRITTO',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `hours_attended` decimal(5,2) DEFAULT '0.00' COMMENT 'Ore effettivamente frequentate dal singolo utente',
  `score` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Esito test finale (es. 28/30 o Idoneo)',
  `completion_date` date DEFAULT NULL COMMENT 'Data esatta di conseguimento titolo',
  `certificate_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link al PDF dell''attestato (se salvato fuori da Media Library)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data creazione record',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento record',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_session` (`training_session_id`,`employee_id`),
  UNIQUE KEY `unique_agent_session` (`training_session_id`,`agent_id`),
  KEY `employee_id` (`employee_id`),
  KEY `agent_id` (`agent_id`),
  CONSTRAINT `training_records_ibfk_1` FOREIGN KEY (`training_session_id`) REFERENCES `training_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_records_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `training_records_ibfk_3` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro presenze e certificazioni: traccia la formazione di agenti e dipendenti per scopi normativi.';


DROP TABLE IF EXISTS `training_sessions`;
CREATE TABLE `training_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco sessione',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tenant che organizza o acquista la formazione',
  `training_template_id` int unsigned DEFAULT NULL COMMENT 'Riferimento al template del corso',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Nome specifico (es. Sessione Autunnale OAM Roma)',
  `total_hours` decimal(5,2) DEFAULT '1.00' COMMENT 'Numero ore effettive erogate in questa sessione',
  `trainer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome del docente o ente formatore',
  `start_date` date DEFAULT NULL COMMENT 'Data inizio corso',
  `end_date` date DEFAULT NULL COMMENT 'Data fine corso',
  `location` enum('ONLINE','PRESENZA','IBRIDO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ONLINE' COMMENT 'Modalità di erogazione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `training_template_id` (`training_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sessioni reali di formazione erogate o pianificate dalle agenzie.';


DROP TABLE IF EXISTS `training_templates`;
CREATE TABLE `training_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco template',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Titolo del corso (es. Aggiornamento Professionale OAM 2024)',
  `category` enum('OAM','IVASS','GDPR','SICUREZZA','PRODOTTO','SOFT_SKILLS') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OAM' COMMENT 'Categoria normativa o tecnica del corso',
  `base_hours` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Numero di ore standard previste per questo corso',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Programma del corso e obiettivi formativi',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se il corso è obbligatorio per legge',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalogo globale: Modelli predefiniti di corsi di formazione.';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'UUID del Tenant di appartenenza (NULL solo per i Super Admin globali)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome e Cognome dell''utente',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email usata per il login',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password hashata tramite bcrypt/argon2',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token per la sessione "Ricordami"',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data registrazione utente',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultimo aggiornamento profilo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Utenti del sistema: SuperAdmin, Titolari, Agenti e Backoffice.';


-- 2026-02-22 19:00:1
