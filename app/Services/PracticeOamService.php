<?php

namespace App\Services;

use App\Models\Company;
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
     *    - inserted_at < $endDate AND perfected_at IS NULL
     *    - OR perfected_at BETWEEN $startDate AND $endDate
     */
    public function syncPracticeOamsForCompany(string $companyId, string $startDate = '2025-07-01', string $endDate = '2026-01-01'): void
    {
        if (empty($companyId)) {
            $companyId = Company::first()->id;
        }

        try {
            DB::beginTransaction();

            // Update practices perfected_at based on commission data
            DB::statement("
                UPDATE practices p
                JOIN (
                    SELECT
                        pc.practice_id,
                        MIN(pc.status_at) AS perfected_at
                    FROM practice_commissions pc
                    JOIN practices p2 ON pc.practice_id = p2.id
                    WHERE pc.status_payment = 'Pratica perfezionata'
                    AND p2.company_id = ?
                    GROUP BY pc.practice_id
                ) AS subquery ON p.id = subquery.practice_id
                SET p.perfected_at = subquery.perfected_at
                WHERE p.company_id = ?
            ", [$companyId, $companyId]);

            // Step 1: Delete all existing practice_oam records for the company
            $deletedCount = PracticeOam::where('company_id', $companyId)
                ->where('is_active', true)
                ->delete();
            Log::info("Deleted {$deletedCount} practice_oam records for company {$companyId}");

            // Step 2: Get practices that meet the criteria
            $practices = Practice::where(function ($query) use ($startDate, $endDate) {
                // Blocco A: (inserted_at nel range E perfected_at null)
                $query
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q
                            ->whereBetween('inserted_at', [$startDate, $endDate])
                            ->whereNull('perfected_at');
                    })
                    // Blocco B: OR (perfected_at nel range)
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('perfected_at', [$startDate, $endDate]);
                    });
            })
                ->whereNull('rejected_at')  // <--- Questa condizione "AND" si applica a entrambi i blocchi sopra
                ->get();

            // Step 3: Insert new practice_oam records
            $insertedCount = 0;

            foreach ($practices as $practice) {
                $commissionSums = $this->getPracticeCommissionSums($practice);
                $somma = $commissionSums['somma'];
                if ($somma > 0) {
                    // Assicuriamoci che entrambi siano oggetti Carbon per un confronto granulare

                    $is_perfected = $practice->perfected_at >= $practice->inserted_at;
                    $mese = 0;
                    if ($is_perfected) {
                        $mese = $practice->perfected_at->month;
                    }
                    if (!$is_perfected) {
                        $commissionSums['compenso_lavorazione'] = $commissionSums['compenso'];
                        $commissionSums['provvigione_lavorazione'] = $commissionSums['provvigione'];

                        $commissionSums['compenso'] = 0;
                        $commissionSums['provvigione'] = 0;
                    }

                    PracticeOam::create([
                        'company_id' => $companyId,
                        'practice_id' => $practice->id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'perfected_at' => $practice->perfected_at,
                        'inserted_at' => $practice->inserted_at,
                        'is_perfected' => $is_perfected,
                        'is_working' => !$is_perfected,
                        'is_convenctioned' => $practice->is_convenctioned,
                        'is_notconvenctioned' => $practice->is_convenctioned,
                        'mese' => $mese,
                        'tipo_prodotto' => $practice->tipo_prodotto,
                        'name' => $practice->principal->name,
                        // Commission sums based on tipo grouping
                        'erogato' => $practice->amount,
                        'compenso' => $commissionSums['compenso'] ?? 0,
                        'compenso_lavorazione' => $commissionSums['compenso_lavorazione'] ?? 0,
                        'compenso_premio' => $commissionSums['premio'] ?? 0,
                        'compenso_rimborso' => $commissionSums['rimborso'] ?? 0,
                        'compenso_assicurazione' => $commissionSums['assicurazione'] ?? 0,
                        'compenso_cliente' => $commissionSums['cliente'] ?? 0,
                        'storno' => $commissionSums['storno'] ?? 0,
                        'provvigione' => $commissionSums['provvigione'] ?? 0,
                        'provvigione_lavorazione' => $commissionSums['provvigione_lavorazione'] ?? 0,
                        'provvigione_premio' => $commissionSums['premioagente'] ?? 0,
                        'provvigione_rimborso' => $commissionSums['rimborso'] ?? 0,
                        'provvigione_assicurazione' => $commissionSums['assicurazione'] ?? null,
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
     * Get OAM code ID for a practice based on its scope
     */
    private function getOamCodeId(Practice $practice): ?int
    {
        if (!$practice->practice_scope_id) {
            return null;
        }

        // Get the OAM code from the practice scope
        $practiceScope = $practice->practiceScope;
        if (!$practiceScope || !$practiceScope->oam_code) {
            return null;
        }

        // Find the OAM code record
        $oamCode = \App\Models\OamCode::where('code', $practiceScope->oam_code)->first();
        return $oamCode ? $oamCode->id : null;
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
        $i = 0;
        // Process each commission
        foreach ($commissions as $commission) {
            $i++;
            //    Log::info('Commission ' . $i . " data for practice {$practice->id}: " . json_encode($commission));
            $amount = $commission->amount ?? 0;
            //  Log::info("Commission data for practice {$practice->id}: " . json_encode($commission));

            $tipo = strtolower($commission->tipo ?? '');
            $name = strtolower($commission->name ?? '');
            $compenso += $amount;

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
                        $compenso += $amount;
                    }
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
            'somma' => $compenso + $provvigione + $premio + $premioagente + $cliente + $storno + $assicurazione,
        ];

        //   Log::info("All commissiona data for practice {$practice->id}: " . json_encode($commissionsa));
        return $commissionsa;
    }

    /**
     * Get statistics about the sync operation
     */
    public function getSyncStats(string $companyId, string $startDate = '2025-07-01', string $endDate = '2026-01-01'): array
    {
        $totalPractices = Practice::where('company_id', $companyId)->count();

        $eligiblePractices = Practice::where('company_id', $companyId)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->where(function ($subQuery) use ($endDate) {
                        $subQuery
                            ->where('inserted_at', '<', $endDate)
                            ->whereNull('perfected_at');
                    })
                    ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                        $subQuery->whereBetween('perfected_at', [$startDate, $endDate]);
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
