<?php

namespace App\Filament\Resources\PrincipalMandates\Pages;

use App\Filament\Resources\PrincipalMandates\PrincipalMandateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalMandates extends ListRecords
{
    protected static string $resource = PrincipalMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
