<?php

namespace App\Filament\Resources\ProformaStatuses\Pages;

use App\Filament\Resources\ProformaStatuses\ProformaStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProformaStatuses extends ListRecords
{
    protected static string $resource = ProformaStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
