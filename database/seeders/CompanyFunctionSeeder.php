<?php
namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyFunction;  // Modello della tabella 'functions'
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyFunctionSeeder extends Seeder
{
    public function run()
    {
        // 1. Creazione dell'Azienda
        $company = Company::firstOrCreate(['name' => 'Mario Rossi Mediazione S.p.A.']);

        // 2. Creazione dei Referenti Interni (Dipendenti)
        $ceo = Employee::firstOrCreate(['email' => 'mario.rossi@azienda.it'], ['first_name' => 'Mario', 'last_name' => 'Rossi', 'role' => 'Amministratore Delegato']);
        $dirCom = Employee::firstOrCreate(['email' => 'luigi.verdi@azienda.it'], ['first_name' => 'Luigi', 'last_name' => 'Verdi', 'role' => 'Direttore Commerciale']);
        $hrManager = Employee::firstOrCreate(['email' => 'anna.bianchi@azienda.it'], ['first_name' => 'Anna', 'last_name' => 'Bianchi', 'role' => 'HR Manager']);
        $itManager = Employee::firstOrCreate(['email' => 'marco.neri@azienda.it'], ['first_name' => 'Marco', 'last_name' => 'Neri', 'role' => 'IT Manager']);
        $legalOfficer = Employee::firstOrCreate(['email' => 'sara.gialli@azienda.it'], ['first_name' => 'Sara', 'last_name' => 'Gialli', 'role' => 'Legal & Compliance Officer']);

        // 3. Creazione degli Outsourcer (Fornitori/Clienti)
        $outCompliance = Client::firstOrCreate(['vat_number' => 'IT11111111111'], ['company_name' => 'Compliance Hub & Partners S.r.l.', 'type' => 'Azienda']);
        $outAudit = Client::firstOrCreate(['vat_number' => 'IT22222222222'], ['company_name' => 'Audit & Risk Consulting S.p.A.', 'type' => 'Azienda']);
        $outIT = Client::firstOrCreate(['vat_number' => 'IT33333333333'], ['company_name' => 'TechSafe Solutions S.r.l.', 'type' => 'Azienda']);
        $outAccounting = Client::firstOrCreate(['vat_number' => 'IT44444444444'], ['company_name' => 'Studio Commercialisti Associati', 'type' => 'Azienda']);
        $outMarketing = Client::firstOrCreate(['vat_number' => 'IT55555555555'], ['company_name' => 'Creative Web Agency', 'type' => 'Azienda']);

        // 4. Mappatura di tutte le funzioni con le relative configurazioni
        $assignments = [
            // --- GOVERNANCE E BUSINESS (Tutto Interno) ---
            'GOV-CDA' => [
                'internal_employee_id' => $ceo->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Continuo', 'contract_expiry_date' => null, 'notes' => 'Gestione diretta del CdA.'
            ],
            'BUS-DIRCOM' => [
                'internal_employee_id' => $dirCom->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => null, 'notes' => 'Coordinamento rete commerciale interna.'
            ],
            'BUS-RETE' => [
                'internal_employee_id' => $dirCom->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => null, 'notes' => 'Monitoraggio vendite e collaboratori.'
            ],
            'BUS-BO' => [
                'internal_employee_id' => $ceo->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Settimanale', 'contract_expiry_date' => null, 'notes' => 'Istruttoria pratiche gestita internamente.'
            ],
            // --- SUPPORTO (Misto Interno/Esterno) ---
            'SUP-AMM' => [
                'internal_employee_id' => $ceo->id, 'external_client_id' => $outAccounting->id, 'is_outsourced' => true,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => Carbon::now()->addYears(2)->format('Y-m-d'), 'notes' => 'Contabilità e bilancio esternalizzati al commercialista.'
            ],
            'SUP-IT' => [
                'internal_employee_id' => $itManager->id, 'external_client_id' => $outIT->id, 'is_outsourced' => true,
                'report_frequency' => 'Trimestrale', 'contract_expiry_date' => Carbon::now()->addYear()->format('Y-m-d'), 'notes' => "Gestione server e firewall. Monitorato dall'IT Manager interno."
            ],
            'SUP-MKTG' => [
                'internal_employee_id' => $dirCom->id, 'external_client_id' => $outMarketing->id, 'is_outsourced' => true,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => Carbon::now()->addMonths(6)->format('Y-m-d'), 'notes' => 'Campagne social e sito web gestiti da agenzia.'
            ],
            'SUP-RECLAMI' => [
                'internal_employee_id' => $legalOfficer->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Su evento', 'contract_expiry_date' => null, 'notes' => "I reclami sono gestiti dall'ufficio legale interno."
            ],
            'SUP-HR' => [
                'internal_employee_id' => $hrManager->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => null, 'notes' => 'Selezione e formazione gestite dalle Risorse Umane.'
            ],
            // --- CONTROLLI DI II E III LIVELLO (Fortemente Esternalizzati) ---
            'CTRL-COMPL' => [
                'internal_employee_id' => $legalOfficer->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Semestrale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Verifica conformità normativa esternalizzata.'
            ],
            'CTRL-RISK' => [
                'internal_employee_id' => $ceo->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Annuale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Risk assessment annuale a cura dello stesso fornitore Compliance.'
            ],
            'CTRL-AML' => [
                'internal_employee_id' => $ceo->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Trimestrale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Presidio Antiriciclaggio, validazione SOS.'
            ],
            'CTRL-AUDIT' => [
                'internal_employee_id' => $ceo->id, 'external_client_id' => $outAudit->id, 'is_outsourced' => true,
                'report_frequency' => 'Annuale', 'contract_expiry_date' => Carbon::now()->addYears(3)->format('Y-m-d'), 'notes' => 'Internal Audit affidato a ente terzo indipendente.'
            ],
            'CTRL-DPO' => [
                'internal_employee_id' => $itManager->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Annuale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Incarico di DPO esterno assegnato alla società di compliance.'
            ],
        ];

        // 5. Inserimento nel database ciclando l'array
        foreach ($assignments as $code => $data) {
            $function = CompanyFunction::where('code', $code)->first();

            if ($function) {
                DB::table('company_function')->updateOrInsert(
                    [
                        'company_id' => $company->id,
                        'function_id' => $function->id,
                    ],
                    array_merge($data, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        }
    }
}
