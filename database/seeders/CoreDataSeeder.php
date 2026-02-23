<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Company;
use App\Models\Principal;
use App\Models\PrincipalContact;
use App\Models\PrincipalMandate;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        $principals = [
            [
                'name' => 'Findomestic Banca Spa',
                'abi' => '03110',
                'mandate_number' => 'MAND-2024-002',
            ],
            [
                'name' => 'IBL Banca Spa',
                'abi' => '03263',
                'mandate_number' => 'MAND-2024-003',
            ],
            [
                'name' => 'Santander Consumer Bank Spa',
                'abi' => '03191',
                'mandate_number' => 'MAND-2024-004',
            ],
            [
                'name' => 'Banca Progetto Spa',
                'abi' => '05015',
                'mandate_number' => 'MAND-2024-005',
            ],
            [
                'name' => 'Compass Banca Spa',
                'abi' => '03069',
                'mandate_number' => 'MAND-2024-006',
            ],
        ];

        foreach ($principals as $bank) {
            Principal::firstOrCreate(
                ['name' => $bank['name'], 'company_id' => $company->id],
                [
                    'abi' => $bank['abi'],
                    'mandate_number' => $bank['mandate_number'],
                    'start_date' => '2024-01-01',
                    'type' => 'Banca',
                    'status' => 'ATTIVO'
                ]
            );
            $principal = Principal::where('name', $bank['name'])->first();

            // Principal Contacts
            PrincipalContact::firstOrCreate(
                ['email' => 'contact@testbank.it'],
                [
                    'principal_id' => $principal->id,
                    'first_name' => 'Giulia',
                    'last_name' => 'Bianchi',
                    'role_title' => 'Area Manager',
                    'department' => 'Ufficio Crediti'
                ]
            );

            // Mandates (Contratti di mandato)
            PrincipalMandate::firstOrCreate(
                ['mandate_number' => 'MAND-2024-001', 'company_id' => $company->id, 'principal_id' => $principal->id],
                [
                    'name' => 'Mandato Principale',
                    'start_date' => '2024-01-01',
                    'status' => 'ATTIVO'
                ]
            );

            // Agents (Rete Commerciale Esterna)
            $agents = [
                [
                    'name' => 'Eurofinanza Mediazioni SRL',
                    'description' => 'Mediatore Creditizio Nazionale',
                    'oam' => 'M456',  // Formato tipico mediatori (M + numero)
                    'type' => 'Mediatore',
                ],
                [
                    'name' => 'Mario Rossi Consulenze',
                    'description' => 'Agente in Attività Finanziaria',
                    'oam' => 'A1234',  // Formato tipico agenti (A + numero)
                    'type' => 'Agente',
                ],
                [
                    'name' => 'Rete Prestiti Direct SPA',
                    'description' => 'Partner Territoriale CQS',
                    'oam' => 'M987',
                    'type' => 'Mediatore',
                ],
                [
                    'name' => 'Studio Finanziario Bianchi SAS',
                    'description' => 'Agenzia Specializzata Pensionati',
                    'oam' => 'A5566',
                    'type' => 'Agente',
                ],
            ];

            foreach ($agents as $agentData) {
                Agent::firstOrCreate(
                    ['name' => $agentData['name'], 'company_id' => $company->id],
                    [
                        'description' => $agentData['description'],
                        'oam' => $agentData['oam'],
                        'type' => $agentData['type'],
                        'is_active' => 1
                    ]
                );
            }
        }
    }
}
