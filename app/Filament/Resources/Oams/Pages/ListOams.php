<?php

namespace App\Filament\Resources\Oams\Pages;

use App\Filament\Resources\Oams\OamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOams extends ListRecords
{
    protected static string $resource = OamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
