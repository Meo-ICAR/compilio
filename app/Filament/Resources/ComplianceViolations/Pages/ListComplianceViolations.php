<?php

namespace App\Filament\Resources\ComplianceViolations\Pages;

use App\Filament\Resources\ComplianceViolations\ComplianceViolationResource;
use Filament\Resources\Pages\ListRecords;

class ListComplianceViolations extends ListRecords
{
    protected static string $resource = ComplianceViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
