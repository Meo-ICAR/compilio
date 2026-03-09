<?php

namespace App\Filament\Resources\SosReports\Pages;

use App\Filament\Resources\SosReports\SosReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSosReports extends ListRecords
{
    protected static string $resource = SosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
