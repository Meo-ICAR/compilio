<?php

namespace App\Filament\Resources\RuiSedis\Pages;

use App\Filament\Resources\RuiSedis\RuiSediResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiSedis extends ListRecords
{
    protected static string $resource = RuiSediResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
