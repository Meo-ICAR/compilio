<?php

namespace App\Filament\Resources\CompanyClients\Pages;

use App\Filament\Resources\CompanyClients\CompanyClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyClients extends ListRecords
{
    protected static string $resource = CompanyClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
