<?php

namespace App\Filament\Resources\GdprControllers\Pages;

use App\Filament\Resources\GdprControllers\GdprControllerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGdprControllers extends ListRecords
{
    protected static string $resource = GdprControllerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
