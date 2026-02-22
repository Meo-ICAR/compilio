<?php

namespace App\Filament\Resources\PracticeCommissions\Pages;

use App\Filament\Resources\PracticeCommissions\PracticeCommissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeCommissions extends ListRecords
{
    protected static string $resource = PracticeCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
