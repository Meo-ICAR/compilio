<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Principal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPrincipalsFromRuiCollaboratori extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rui:sync-principals-from-collaboratori
                            {--company-id= : Process specific company only}
                            {--batch=1000 : Number of records to process in each batch}
                            {--dry-run : Show what would be done without making changes}
                            {--force : Force update even if names match}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync principals from rui_collaboratori based on RUI registration numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing principals from RUI collaboratori...');

        $companyId = $this->option('company-id');
        $batchSize = (int) $this->option('batch');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Get companies to process - only those with numero_iscrizione_rui
        $companiesQuery = Company::select('id', 'name', 'numero_iscrizione_rui')
            ->whereNotNull('numero_iscrizione_rui')
            ->where('numero_iscrizione_rui', '!=', '');

        if ($companyId) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get();

        foreach ($companies as $company) {
            $this->processCompany($company, $dryRun, $force);
        }

        $this->info('✅ Principal sync completed!');
        return 0;
    }

    private function processCompany($company, bool $dryRun, bool $force)
    {
        $this->line("\n📋 Processing company: {$company->name} ({$company->id})");
        $this->line("  🎯 RUI Number: {$company->numero_iscrizione_rui}");

        // Get all principals for this company that have empty numero_iscrizione_rui

        // Get principal collaboratori for matching
        $principalCollaboratori = $company
            ->ruiCollaboratoriPrincipal()
            ->select('num_iscr_intermediario', 'intermediario', 'collaboratore')
            ->distinct()
            ->get();

        if ($principalCollaboratori->isEmpty()) {
            $this->line('  ℹ️  No principals with empty RUI numbers found');
            return;
        }

        $this->line('  🔍 Found ' . $$principalCollaboratori->count() . ' principals to check');

        $processedCount = 0;
        $updatedCount = 0;

        foreach ($principalCollaboratori as $principalCollaboratore) {
            $result = $this->processPrincipal($principalCollaboratore, $dryRun, $force);

            if ($result['processed']) {
                $processedCount++;
                if ($result['updated'])
                    $updatedCount++;
            }
        }

        $this->line("  📊 Results: {$processedCount} processed, {$updatedCount} updated");
    }

    private function processPrincipal($principalCollaboratore, bool $dryRun, bool $force): array
    {
        $intermediarioName = $principalCollaboratore->intermediario ?? '';
        $ruiNumber = $principalCollaboratore->num_iscr_intermediario ?? '';

        if (empty($ruiNumber) || empty($intermediarioName)) {
            return ['processed' => false, 'updated' => false];
        }

        // Find principal to update by matching name with intermediario
        $principal = Principal::where('name', $intermediarioName)
            ->where(function ($query) {
                $query
                    ->whereNull('numero_iscrizione_rui')
                    ->orWhere('numero_iscrizione_rui', '');
            })
            ->first();

        if (!$principal) {
            if (!$dryRun) {
                Principal::create([
                    'name' => $intermediarioName,
                    'oam_name' => $intermediarioName,
                    'numero_iscrizione_rui' => $ruiNumber,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $this->line("    🔄 Created principal '{$intermediarioName}' -> OAM: '{$intermediarioName}', RUI: {$ruiNumber}");
        }
        if ($principal) {
            if (!$dryRun) {
                $principal->update([
                    'oam_name' => $intermediarioName,
                    'numero_iscrizione_rui' => $ruiNumber,
                    'updated_at' => now(),
                ]);
            }
        }

        $this->line("    🔄 Update principal '{$intermediarioName}' -> OAM: '{$intermediarioName}', RUI: {$ruiNumber}");
        return ['processed' => true, 'updated' => true];
    }
}
