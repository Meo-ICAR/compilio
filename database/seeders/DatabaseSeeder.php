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
            PracticeCommissionStatusSeeder::class,
            FilamentUserSeeder::class,
            AmlChecklistSeeder::class,
            AuditChecklistSeeder::class,
            BankAuditCompanyChecklistSeeder::class,
            CessioneQuintoChecklistSeeder::class,
            MutuoImmobiliareChecklistSeeder::class,
            ComplianceSeeder::class,
        ]);
    }
}
