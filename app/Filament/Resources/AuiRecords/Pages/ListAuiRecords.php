<?php

namespace App\Filament\Resources\AuiRecords\Pages;

use App\Filament\Resources\AuiRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAuiRecords extends ListRecords
{
    protected static string $resource = AuiRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
