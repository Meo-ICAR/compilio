<?php

namespace App\Filament\Resources\OAMSoggettis\Pages;

use App\Filament\Resources\OAMSoggettis\OAMSoggettiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOAMSoggettis extends ListRecords
{
    protected static string $resource = OAMSoggettiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
