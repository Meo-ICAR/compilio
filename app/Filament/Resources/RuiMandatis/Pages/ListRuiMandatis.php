<?php

namespace App\Filament\Resources\RuiMandatis\Pages;

use App\Filament\Resources\RuiMandatis\RuiMandatiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiMandatis extends ListRecords
{
    protected static string $resource = RuiMandatiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
