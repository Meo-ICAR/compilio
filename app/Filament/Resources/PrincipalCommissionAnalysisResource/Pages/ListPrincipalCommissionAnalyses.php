<?php

namespace App\Filament\Resources\PrincipalCommissionAnalysisResource\Pages;

use App\Filament\Resources\PrincipalCommissionAnalysisResource;
use App\Filament\Widgets\PrincipalCommissionOverview;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalCommissionAnalyses extends ListRecords
{
    protected static string $resource = PrincipalCommissionAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Nessuna azione di creazione - è solo visualizzazione
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrincipalCommissionOverview::class,
        ];
    }
}
