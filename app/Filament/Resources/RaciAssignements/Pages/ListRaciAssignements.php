<?php

namespace App\Filament\Resources\RaciAssignements\Pages;

use App\Filament\Resources\RaciAssignements\RaciAssignementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRaciAssignements extends ListRecords
{
    protected static string $resource = RaciAssignementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
