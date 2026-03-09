<?php

namespace App\Filament\Resources\ChecklistAudits\Pages;

use App\Filament\Resources\ChecklistAudits\ChecklistAuditResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChecklistAudits extends ListRecords
{
    protected static string $resource = ChecklistAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
