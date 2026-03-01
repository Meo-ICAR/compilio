<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LookupSeeder::class,
            TenantSeeder::class,
            CoreDataSeeder::class,
            OperationalSeeder::class,
            SupportSeeder::class,
            OamScopeSeeder::class,
            FirrSeeder::class,
            FinancialSeeder::class,
            PracticeCommissionStatusSeeder::class,
            PracticeStatusLookupSeeder::class,
            DocumentStatusSeederFixed::class,
            DocumentStatusSeeder::class,
            DocumentTypeSeeder::class,
            FilamentUserSeeder::class,
            AmlChecklistSeeder::class,
            AuditChecklistSeeder::class,
            CessioneQuintoChecklistSeeder::class,
            MutuoImmobiliareChecklistSeeder::class,
            BankAuditCompanyChecklistSeeder::class,
            ComplianceSeeder::class,
            RemediationSeeder::class,
            BusinessFunctionSeeder::class,
            CompanyFunctionSeeder::class,
            FunctionPrivacySeeder::class,

            /*
             * AbiSeeder::class,
             * OamSeeder::class,
             * AgenteInAttivitaFinanziariaSeeder::class,
             * ComuneSeeder::class,
             */
        ]);
    }
}
