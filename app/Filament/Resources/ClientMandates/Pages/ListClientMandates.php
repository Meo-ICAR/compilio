<?php

namespace App\Filament\Resources\ClientMandates\Pages;

use App\Filament\Resources\ClientMandates\ClientMandateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientMandates extends ListRecords
{
    protected static string $resource = ClientMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
