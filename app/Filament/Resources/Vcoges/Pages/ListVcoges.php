<?php

namespace App\Filament\Resources\Vcoges\Pages;

use App\Filament\Resources\Vcoges\VcogesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVcoges extends ListRecords
{
    protected static string $resource = VcogesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
