<?php

namespace App\Services;

use App\Models\Company;
use App\Models\OamCode;
use App\Models\Practice;
use App\Models\PracticeCommission;
use App\Models\PracticeOam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class PracticeOamService
{
    /**
     * Sync practice_oams for a company:
     * 1. Delete all existing practice_oam records for the company
     * 2. Insert practices that meet the criteria:
     *    - inserted_at < $endDate AND erogated_at IS NULL
     *    - OR erogated_at BETWEEN $startDate AND $endDate
     */
    public function syncPracticeOamsForCompany(string $companyId, ?string $startDate, ?string $endDate): void
    {
        if (empty($companyId)) {
            $companyId = Company::first()->id;
        }
        if (empty($startDate)) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            if (now()->month() < 5) {
                $startDate = Carbon::parse($startDate)->subMonths(6)->format('Y-m-d');
            }
        }
        // Always calculate endDate based on the final startDate value
        if (empty($endDate)) {
            $endDate = Carbon::parse($startDate)->addMonths(6)->format('Y-m-d');
        }

        try {
            DB::beginTransaction();

            // Step 1: Delete all existing practice_oam records for the company
            $deletedCount = PracticeOam::where('company_id', $companyId)
                ->where('is_active', true)
                ->delete();
            Log::info("Deleted {$deletedCount} practice_oam records for company {$companyId}");

            // Step 2: Get practices that meet the criteria
            $practices = Practice::where(function ($query) use ($startDate, $endDate) {
                // Practices sent before end date AND (perfected after start date OR not perfected yet)
                $query
                    ->where('sended_at', '<', $endDate)
                    ->where(function ($q) use ($startDate) {
                        $q
                            ->where('erogated_at', '>=', $startDate)
                            ->orWhereNull('erogated_at');
                    });
            })
                ->whereNull('rejected_at')
                ->get();

            // Step 3: Insert new practice_oam records
            $insertedCount = 0;

            foreach ($practices as $practice) {
                $erogato = $practice->amount;
                $erogato_lavorazione = 0;
                $commissionSums = $this->getPracticeCommissionSums($practice);
                $somma = $commissionSums['somma'];
                if ($somma > 0) {
                    // Assicuriamoci che entrambi siano oggetti Carbon per un confronto granulare

                    $is_perfected = $practice->erogated_at >= $practice->inserted_at;
                    $is_perfected = $is_perfected && ($practice->erogated_at < $endDate);
                    $mese = 0;
                    if ($is_perfected) {
                        $mese = $practice->erogated_at->month;
                    }
                    $tipoProdotto = $practice->tipo_prodotto;
                    $compenso = $commissionSums['compenso'];
                    $premio = $commissionSums['premio'];
                    $assicurazione = $commissionSums['assicurazione'];

                    if (!($tipoProdotto == 'Non so')) {
                        $compenso = $compenso + $premio + $assicurazione;
                        $premio = 0;
                        $assicurazione = 0;
                    }
                    if (!$is_perfected) {
                        $commissionSums['compenso_lavorazione'] = $commissionSums['compenso'];
                        $commissionSums['provvigione_lavorazione'] = $commissionSums['provvigione'];
                        $erogato_lavorazione = $erogato;

                        $commissionSums['compenso'] = 0;
                        $erogato = 0;
                        $commissionSums['provvigione'] = 0;
                    }

                    PracticeOam::create([
                        'company_id' => $companyId,
                        'practice_id' => $practice->id,
                        'oam_code' => $practice?->practiceScope?->oam_code,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'perfected_at' => $practice->erogated_at,
                        'inserted_at' => $practice->inserted_at,
                        'is_perfected' => $is_perfected,
                        'is_working' => !$is_perfected,
                        'is_conventioned' => $compenso > 0,
                        'is_notconventioned' => !($compenso > 0),
                        'mese' => $mese,
                        'tipo_prodotto' => $tipoProdotto,
                        'name' => $practice->principal->name,
                        // Commission sums based on tipo grouping
                        'erogato' => $erogato ?? 0,
                        'erogato_lavorazione' => $erogato_lavorazione ?? 0,
                        'compenso' => $compenso ?? 0,
                        'compenso_lavorazione' => $commissionSums['compenso_lavorazione'] ?? 0,
                        'compenso_premio' => $premio ?? 0,  // premio assicurativo
                        'compenso_rimborso' => $commissionSums['rimborso'] ?? 0,
                        'compenso_assicurazione' => $assicurazione ?? 0,
                        'compenso_cliente' => $commissionSums['cliente'] ?? 0,
                        'storno' => $commissionSums['storno'] ?? 0,
                        'provvigione' => $commissionSums['provvigione'] ?? 0,
                        'provvigione_lavorazione' => $commissionSums['provvigione_lavorazione'] ?? 0,
                        'provvigione_premio' => $commissionSums['premioagente'] ?? 0,
                        'provvigione_rimborso' => $commissionSums['rimborso'] ?? 0,
                        'provvigione_assicurazione' => $commissionSums['provvigione_assicurazione'] ?? null,
                        'provvigione_storno' => $commissionSums['storno'] ?? null,
                    ]);
                    $insertedCount++;
                }
            }

            DB::commit();

            Log::info("Sync completed for company {$companyId}: {$insertedCount} practice_oam records inserted");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error syncing practice_oams for company {$companyId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get practice commission sums grouped by tipo
     */
    private function getPracticeCommissionSums(Practice $practice): array
    {
        $commissions = PracticeCommission::where('practice_id', $practice->id)->get();

        // Debug: Check if commissions exist
        $n = $commissions->count();
        if ($n > 0) {
            //  Log::info("Practice {$practice->id} has {$n} commissions");
        }

        // Initialize sums
        $compenso = 0;
        $cliente = 0;
        $provvigione = 0;
        $premio = 0;
        $premioagente = 0;
        $assicurazione = 0;
        $storno = 0;
        $somma = 0;
        $erogato = 0;

        $i = 0;
        // Process each commission
        foreach ($commissions as $commission) {
            $i++;
            //    Log::info('Commission ' . $i . " data for practice {$practice->id}: " . json_encode($commission));
            $amount = $commission->amount ?? 0;
            //  Log::info("Commission data for practice {$practice->id}: " . json_encode($commission));

            $tipo = strtolower($commission->tipo ?? '');
            $name = strtolower($commission->name ?? '');

            // Non-agent, non-client commissions
            if ($tipo === 'istituto') {
                $compenso += $amount;
                //
                // Non-agent premiums
                if (strpos($name, 'premio')) {
                    $premio += $amount;
                } else {
                    if (strpos($name, 'polizza') || strpos($name, 'broker')) {
                        $assicurazione += $amount;
                    } else {
                        //   $compenso += $amount;
                    }
                }
            }

            if ($tipo === 'cliente') {
                $cliente += $amount;
            }

            // Agent commissions
            if ($tipo === 'agente') {
                // Agent premiums
                if (strpos($name, 'premio') !== false) {
                    $premioagente += $amount;
                } else {
                    $provvigione += $amount;
                }
            }

            //
            // Storno
            if ($commission->is_storno) {
                $storno += $amount;
                continue;
            }
        }

        $commissionsa = [
            'compenso' => $compenso - $premio ?: 0,
            'cliente' => $cliente ?: 0,
            'provvigione' => $provvigione - $premioagente ?: 0,
            'premio' => $premio ?: 0,
            'premioagente' => $premioagente ?: 0,
            'storno' => $storno ?: 0,
            'assicurazione' => $assicurazione ?: 0,
            'provvigione_assicurazione' => $assicurazione ?: 0,
            'somma' => $compenso + $provvigione + $premio + $premioagente + $cliente + $storno + $assicurazione,
        ];

        if ($practice->CRM_code == 'QT00919') {
            Log::info("All commissiona data for practice {$practice->id}: " . json_encode($commissionsa));
        }
        return $commissionsa;
    }

    /**
     * Get statistics about the sync operation
     */
    public function getSyncStats(string $companyId, ?string $startDate = null, ?string $endDate = null): array
    {
        // Use default values if null
        if (empty($startDate)) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            if (now()->month() < 5) {
                $startDate = Carbon::parse($startDate)->subMonths(6)->format('Y-m-d');
            }
        }
        if (empty($endDate)) {
            $endDate = Carbon::parse($startDate)->addMonths(6)->format('Y-m-d');
        }

        $totalPractices = Practice::where('company_id', $companyId)->count();

        $eligiblePractices = Practice::where('company_id', $companyId)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->where(function ($subQuery) use ($endDate) {
                        $subQuery
                            ->where('inserted_at', '<', $endDate)
                            ->whereNull('erogated_at');
                    })
                    ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                        $subQuery->whereBetween('erogated_at', [$startDate, $endDate]);
                    });
            })
            ->count();

        $currentPracticeOams = PracticeOam::where('company_id', $companyId)->count();

        return [
            'total_practices' => $totalPractices,
            'eligible_practices' => $eligiblePractices,
            'current_practice_oams' => $currentPracticeOams,
            'needs_sync' => $currentPracticeOams !== $eligiblePractices,
        ];
    }

    /**
     * Bulk sync for multiple companies
     */
    public function syncPracticeOamsForMultipleCompanies(array $companyIds, string $startDate = '2025-07-01', string $endDate = '2026-01-01'): array
    {
        $results = [];

        foreach ($companyIds as $companyId) {
            if (empty($companyId)) {
                $results[$companyId] = [
                    'success' => false,
                    'message' => 'Company ID cannot be empty',
                ];
                continue;
            }

            try {
                $this->syncPracticeOamsForCompany($companyId, $startDate, $endDate);
                $stats = $this->getSyncStats($companyId, $startDate, $endDate);

                $results[$companyId] = [
                    'success' => true,
                    'message' => 'Sync completed successfully',
                    'stats' => $stats,
                ];
            } catch (\Exception $e) {
                $results[$companyId] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
