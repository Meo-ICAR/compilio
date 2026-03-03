<?php

namespace App\Filament\Resources\OamScopes\Pages;

use App\Filament\Resources\OamScopes\OamScopeResource;
use Filament\Resources\Pages\ListRecords;

class ListOamScopes extends ListRecords
{
    protected static string $resource = OamScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
