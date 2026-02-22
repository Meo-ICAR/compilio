<?php

namespace App\Filament\Resources\CompanyWebsites\Pages;

use App\Filament\Resources\CompanyWebsites\CompanyWebsiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyWebsites extends ListRecords
{
    protected static string $resource = CompanyWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
