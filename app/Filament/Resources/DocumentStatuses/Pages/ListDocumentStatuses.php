<?php

namespace App\Filament\Resources\DocumentStatuses\Pages;

use App\Filament\Resources\DocumentStatuses\DocumentStatusResource;
use Filament\Resources\Pages\ListRecords;

class ListDocumentStatuses extends ListRecords
{
    protected static string $resource = DocumentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
