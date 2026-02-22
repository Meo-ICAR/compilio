<?php

namespace App\Filament\Resources\RegulatoryBodyScopes\Pages;

use App\Filament\Resources\RegulatoryBodyScopes\RegulatoryBodyScopeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegulatoryBodyScopes extends ListRecords
{
    protected static string $resource = RegulatoryBodyScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
