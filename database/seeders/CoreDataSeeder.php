<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Principal;
use App\Models\PrincipalContact;
use App\Models\Agent;
use App\Models\Mandate;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) return;

        // Principals (Banche Mandanti)
        $principal = Principal::firstOrCreate(
            ['name' => 'Test Bank Spa', 'company_id' => $company->id],
            [
                'abi' => '01234',
                'mandate_number' => 'MAND-2024-001',
                'start_date' => '2024-01-01',
                'type' => 'Banca',
                'status' => 'ATTIVO'
            ]
        );

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
        Mandate::firstOrCreate(
            ['mandate_number' => 'MAND-2024-001', 'company_id' => $company->id, 'principal_id' => $principal->id],
            [
                'name' => 'Mandato Principale',
                'start_date' => '2024-01-01',
                'status' => 'ATTIVO'
            ]
        );

        // Agents (Rete Commerciale Esterna)
        Agent::firstOrCreate(
            ['name' => 'Mega Consulenze SRL', 'company_id' => $company->id],
            [
                'description' => 'Agenzia OAM Partner',
                'oam' => 'OAM99999',
                'type' => 'Mediatore',
                'is_active' => 1
            ]
        );
    }
}
