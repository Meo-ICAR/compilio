<?php

namespace App\Filament\Resources\Venasarcotrimestres\Pages;

use App\Filament\Resources\Venasarcotrimestres\VenasarcotrimestreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVenasarcotrimestres extends ListRecords
{
    protected static string $resource = VenasarcotrimestreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
