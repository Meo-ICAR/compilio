<?php

namespace App\Filament\Resources\EnasarcoLimits\Pages;

use App\Filament\Resources\EnasarcoLimits\EnasarcoLimitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnasarcoLimits extends ListRecords
{
    protected static string $resource = EnasarcoLimitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
