<?php

namespace App\Filament\Resources\Checklists\ChecklistResource\Pages;

use App\Filament\Resources\Checklists\ChecklistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChecklists extends ListRecords
{
    protected static string $resource = ChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
