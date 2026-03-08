<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Company;
use Illuminate\Console\Command;

class CompanyUpdateAddressesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:update-addresses
                            {--from-rui-sedi : Update addresses from RUI sede data}
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update company addresses from RUI sede data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('🔄 Updating company addresses from RUI sede data...');

        $companies = Company::whereNotNull('numero_iscrizione_rui')
            ->with('ruiSedi')
            ->get();

        if ($companies->isEmpty()) {
            $this->warn('No companies found with RUI numbers.');
            return 0;
        }

        $updatedCount = 0;
        foreach ($companies as $company) {
            if (!$company->ruiSedi) {
                $this->line("⚠️  Company '{$company->name}' has no RUI sede data");
                continue;
            }

            if ($dryRun) {
                $this->line("🔍 DRY RUN: Would create address for '{$company->name}'");
                $this->line("   Address: {$company->ruiSedi->indirizzo_sede}");
                $this->line("   City: {$company->ruiSedi->comune_sede}");
                $this->line("   Province: {$company->ruiSedi->provincia_sede}");
                $this->line("   Postal Code: {$company->ruiSedi->cap_sede}");
            } else {
                // Create new address record using polymorphic relationship
                Address::create([
                    'name' => 'Sede legale',
                    'street' => $company->ruiSedi->indirizzo_sede,
                    'city' => $company->ruiSedi->comune_sede,
                    'zip_code' => $company->ruiSedi->cap_sede,
                    'address_type_id' => 1,
                    'addressable_type' => Company::class,
                    'addressable_id' => $company->id,
                ]);

                $this->line("✅ Created address for '{$company->name}'");
                $updatedCount++;
            }
        }

        $this->info('✅ Address update completed!');
        $this->line("📊 Results: {$updatedCount} companies updated");

        return 0;
    }
}
