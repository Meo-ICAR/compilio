<?php

namespace App\Filament\Resources\DocumentTypeScopes\Pages;

use App\Filament\Resources\DocumentTypeScopes\DocumentTypeScopeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTypeScopes extends ListRecords
{
    protected static string $resource = DocumentTypeScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
