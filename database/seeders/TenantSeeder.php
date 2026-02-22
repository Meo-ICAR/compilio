<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\CompanyWebsite;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Tenant (Company)
        $company = Company::firstOrCreate(
            ['vat_number' => 'IT12345678901'],
            [
                'id' => Str::uuid(),
                'name' => 'Main Agency HQ S.p.A.',
                'vat_name' => 'Main Agency',
                'oam' => 'OAM12345',
                'oam_at' => '2020-01-01',
                'oam_name' => 'Main Agency HQ'
            ]
        );

        // 2. Company Branches
        $branch = CompanyBranch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Sede Legale Milano'],
            [
                'is_main_office' => 1,
                'manager_first_name' => 'Mario',
                'manager_last_name' => 'Rossi'
            ]
        );

        // 3. Company Websites
        CompanyWebsite::firstOrCreate(
            ['domain' => 'www.mainagency.it'],
            [
                'company_id' => $company->id,
                'name' => 'Portale Principale',
                'type' => 'Vetrina',
                'is_active' => 1
            ]
        );

        // 4. Super Admin (Global)
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'company_id' => null
            ]
        );

        // 5. Tenant Users (Agents & Admins)
        $tenantAdmin = User::firstOrCreate(
            ['email' => 'user@agency.com'],
            [
                'name' => 'Tenant Manager',
                'password' => Hash::make('password'),
                'company_id' => $company->id
            ]
        );

        $agentUser = User::firstOrCreate(
            ['email' => 'agent@agency.com'],
            [
                'name' => 'Luigi Agente',
                'password' => Hash::make('password'),
                'company_id' => $company->id
            ]
        );

        // 6. Employees (Internal Staff)
        Employee::firstOrCreate(
            ['user_id' => $tenantAdmin->id],
            [
                'company_id' => $company->id,
                'name' => 'Tenant Manager',
                'role_title' => 'Amministratore',
                'email' => 'user@agency.com',
                'company_branche_id' => $branch->id,
            ]
        );
    }
}
