<?php

namespace App\Filament\Resources\CompanyWebsites\Pages;

use App\Filament\Resources\CompanyWebsites\CompanyWebsiteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyWebsite extends EditRecord
{
    protected static string $resource = CompanyWebsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
