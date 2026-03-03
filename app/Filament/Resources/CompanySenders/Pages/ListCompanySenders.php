<?php

namespace App\Filament\Resources\CompanySenders\Pages;

use App\Filament\Resources\CompanySenders\CompanySenderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanySenders extends ListRecords
{
    protected static string $resource = CompanySenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
