<?php

namespace App\Filament\Resources\PrincipalContacts\Pages;

use App\Filament\Resources\PrincipalContacts\PrincipalContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalContacts extends ListRecords
{
    protected static string $resource = PrincipalContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
