<?php

namespace App\Filament\Resources\RuiWebSites\Pages;

use App\Filament\Resources\RuiWebSites\RuiWebSitesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiWebSites extends ListRecords
{
    protected static string $resource = RuiWebSitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
