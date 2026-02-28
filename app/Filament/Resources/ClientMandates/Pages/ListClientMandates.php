<?php

namespace App\Filament\Resources\ClientMandates\Pages;

use App\Filament\Resources\ClientMandateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClientMandates extends ListRecords
{
    protected static string $resource = ClientMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
