<?php
namespace Database\Seeders;

use App\Models\BusinessFunction;
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
        $ceo = Employee::firstOrCreate(['email' => 'mario.rossi@azienda.it'], ['name' => 'Mario Rossi', 'role_title' => 'Amministratore Delegato']);
        $dirCom = Employee::firstOrCreate(['email' => 'luigi.verdi@azienda.it'], ['name' => 'Luigi Verdi', 'role_title' => 'Direttore Commerciale']);
        $hrManager = Employee::firstOrCreate(['email' => 'anna.bianchi@azienda.it'], ['name' => 'Anna Bianchi', 'role_title' => 'HR Manager']);
        $itManager = Employee::firstOrCreate(['email' => 'marco.neri@azienda.it'], ['name' => 'Marco Neri', 'role_title' => 'IT Manager']);
        $legalOfficer = Employee::firstOrCreate(['email' => 'sara.gialli@azienda.it'], ['name' => 'Sara Gialli', 'role_title' => 'Legal & Compliance Officer']);

        // 3. Creazione degli Outsourcer (Fornitori/Clienti)
        $outCompliance = Client::firstOrCreate(['tax_code' => 'IT11111111111'], ['name' => 'Compliance Hub & Partners S.r.l.', 'client_type_id' => 21]);
        $outAudit = Client::firstOrCreate(['tax_code' => 'IT22222222222'], ['name' => 'Audit & Risk Consulting S.p.A.', 'client_type_id' => 21]);
        $outIT = Client::firstOrCreate(['tax_code' => 'IT33333333333'], ['name' => 'TechSafe Solutions S.r.l.', 'client_type_id' => 21]);
        $outAccounting = Client::firstOrCreate(['tax_code' => 'IT44444444444'], ['name' => 'Studio Commercialisti Associati', 'client_type_id' => 21]);
        $outMarketing = Client::firstOrCreate(['tax_code' => 'IT55555555555'], ['name' => 'Creative Web Agency', 'client_type_id' => 21]);

        // 4. Mappatura di tutte le funzioni con le relative configurazioni
        $assignments = [
            // --- GOVERNANCE E BUSINESS (Tutto Interno) ---
            'GOV-CDA' => [
                'employee_id' => $ceo->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Continuo', 'contract_expiry_date' => null, 'notes' => 'Gestione diretta del CdA.'
            ],
            'BUS-DIRCOM' => [
                'employee_id' => $dirCom->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => null, 'notes' => 'Coordinamento rete commerciale interna.'
            ],
            'BUS-RETE' => [
                'employee_id' => $dirCom->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => null, 'notes' => 'Monitoraggio vendite e collaboratori.'
            ],
            'BUS-BO' => [
                'employee_id' => $ceo->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Settimanale', 'contract_expiry_date' => null, 'notes' => 'Istruttoria pratiche gestita internamente.'
            ],
            // --- SUPPORTO (Misto Interno/Esterno) ---
            'SUP-AMM' => [
                'employee_id' => $ceo->id, 'external_client_id' => $outAccounting->id, 'is_outsourced' => true,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => Carbon::now()->addYears(2)->format('Y-m-d'), 'notes' => 'Contabilità e bilancio esternalizzati al commercialista.'
            ],
            'SUP-IT' => [
                'employee_id' => $itManager->id, 'external_client_id' => $outIT->id, 'is_outsourced' => true,
                'report_frequency' => 'Trimestrale', 'contract_expiry_date' => Carbon::now()->addYear()->format('Y-m-d'), 'notes' => "Gestione server e firewall. Monitorato dall'IT Manager interno."
            ],
            'SUP-MKTG' => [
                'employee_id' => $dirCom->id, 'external_client_id' => $outMarketing->id, 'is_outsourced' => true,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => Carbon::now()->addMonths(6)->format('Y-m-d'), 'notes' => 'Campagne social e sito web gestiti da agenzia.'
            ],
            'SUP-RECLAMI' => [
                'employee_id' => $legalOfficer->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Su evento', 'contract_expiry_date' => null, 'notes' => "I reclami sono gestiti dall'ufficio legale interno."
            ],
            'SUP-HR' => [
                'employee_id' => $hrManager->id, 'external_client_id' => null, 'is_outsourced' => false,
                'report_frequency' => 'Mensile', 'contract_expiry_date' => null, 'notes' => 'Selezione e formazione gestite dalle Risorse Umane.'
            ],
            // --- CONTROLLI DI II E III LIVELLO (Fortemente Esternalizzati) ---
            'CTRL-COMPL' => [
                'employee_id' => $legalOfficer->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Semestrale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Verifica conformità normativa esternalizzata.'
            ],
            'CTRL-RISK' => [
                'employee_id' => $ceo->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Annuale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Risk assessment annuale a cura dello stesso fornitore Compliance.'
            ],
            'CTRL-AML' => [
                'employee_id' => $ceo->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
                'report_frequency' => 'Trimestrale', 'contract_expiry_date' => Carbon::now()->addYears(1)->format('Y-m-d'), 'notes' => 'Presidio Antiriciclaggio, validazione SOS.'
            ],
            'CTRL-AUDIT' => [
                'employee_id' => $ceo->id, 'external_client_id' => $outAudit->id, 'is_outsourced' => true,
                'report_frequency' => 'Annuale', 'contract_expiry_date' => Carbon::now()->addYears(3)->format('Y-m-d'), 'notes' => 'Internal Audit affidato a ente terzo indipendente.'
            ],
            'CTRL-DPO' => [
                'employee_id' => $itManager->id, 'external_client_id' => $outCompliance->id, 'is_outsourced' => true,
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
                        'business_function_id' => $function->id,
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
