<?php

namespace App\Filament\Resources\Ruis\Pages;

use App\Filament\Resources\Ruis\RuiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuis extends ListRecords
{
    protected static string $resource = RuiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
