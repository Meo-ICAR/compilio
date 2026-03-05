<?php

namespace App\Filament\Resources\RuiSezds\Pages;

use App\Filament\Resources\RuiSezds\RuiSezdsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiSezds extends ListRecords
{
    protected static string $resource = RuiSezdsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
