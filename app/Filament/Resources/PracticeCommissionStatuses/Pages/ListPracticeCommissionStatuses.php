<?php

namespace App\Filament\Resources\PracticeCommissionStatuses\Pages;

use App\Filament\Resources\PracticeCommissionStatuses\PracticeCommissionStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeCommissionStatuses extends ListRecords
{
    protected static string $resource = PracticeCommissionStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
