<?php

namespace App\Filament\Resources\Processes\Pages;

use App\Filament\Resources\Processes\ProcessResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProcesses extends ManageRecords
{
    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
