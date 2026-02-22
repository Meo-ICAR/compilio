<?php

namespace App\Filament\Resources\SoftwareMappings\Pages;

use App\Filament\Resources\SoftwareMappings\SoftwareMappingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftwareMappings extends ListRecords
{
    protected static string $resource = SoftwareMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
