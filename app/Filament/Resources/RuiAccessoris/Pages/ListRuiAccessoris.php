<?php

namespace App\Filament\Resources\RuiAccessoris\Pages;

use App\Filament\Resources\RuiAccessoris\RuiAccessorisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiAccessoris extends ListRecords
{
    protected static string $resource = RuiAccessorisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
