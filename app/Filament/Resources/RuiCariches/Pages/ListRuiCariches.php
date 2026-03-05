<?php

namespace App\Filament\Resources\RuiCariches\Pages;

use App\Filament\Resources\RuiCariches\RuiCaricheResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiCariches extends ListRecords
{
    protected static string $resource = RuiCaricheResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
