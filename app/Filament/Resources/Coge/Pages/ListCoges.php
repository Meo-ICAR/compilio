<?php

namespace App\Filament\Resources\Coge\Pages;

use App\Filament\Resources\Coge\CogeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoges extends ListRecords
{
    protected static string $resource = CogeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
