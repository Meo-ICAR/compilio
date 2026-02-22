<?php

namespace App\Filament\Resources\AuditItems\Pages;

use App\Filament\Resources\AuditItems\AuditItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuditItems extends ListRecords
{
    protected static string $resource = AuditItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
