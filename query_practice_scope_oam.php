<?php

use App\Models\Practice;
use Illuminate\Support\Carbon;

// Query per selezionare pratiche con practiceScopeOAM() non null
// e con condizioni specifiche su isWorking() e perfected_at

$practices = Practice::whereHas('practiceScope', function($query) {
        $query->whereNotNull('oam_code');
    })
    ->where(function($query) {
        // Condizione 1: isWorking() true e inserted_at < fine anno precedente
        $query->whereHas('practiceStatus', function($statusQuery) {
                $statusQuery->where('is_working', true);
            })
            ->where('inserted_at', '<', Carbon::now()->subYear()->endOfYear())
            
            // OR
            
            // Condizione 2: perfected_at null OR perfected_at > fine anno precedente
            ->orWhere(function($perfectedQuery) {
                $perfectedQuery->whereNull('perfected_at')
                    ->orWhere('perfected_at', '>', Carbon::now()->subYear()->endOfYear());
            });
    })
    ->get();

// Per ottenere solo i campi richiesti
$results = Practice::whereHas('practiceScope', function($query) {
        $query->whereNotNull('oam_code');
    })
    ->where(function($query) {
        $query->whereHas('practiceStatus', function($statusQuery) {
                $statusQuery->where('is_working', true);
            })
            ->where('inserted_at', '<', Carbon::now()->subYear()->endOfYear())
            ->orWhere(function($perfectedQuery) {
                $perfectedQuery->whereNull('perfected_at')
                    ->orWhere('perfected_at', '>', Carbon::now()->subYear()->endOfYear());
            });
    })
    ->select(['id', 'inserted_at', 'perfected_at'])
    ->with(['practiceScope:oam_code,code,name', 'practiceStatus:is_working,is_rejected,is_perfectioned'])
    ->get();

// Formato output
foreach ($results as $practice) {
    echo "Practice ID: {$practice->id}\n";
    echo "Inserted At: {$practice->inserted_at}\n";
    echo "Perfected At: " . ($practice->perfected_at ?? 'NULL') . "\n";
    echo "Practice Scope OAM: " . $practice->practiceScopeOAM() . "\n";
    echo "Is Working: " . ($practice->isWorking() ? 'true' : 'false') . "\n";
    echo "---\n";
}
