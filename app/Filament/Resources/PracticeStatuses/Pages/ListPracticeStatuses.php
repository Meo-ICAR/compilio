<?php

namespace App\Filament\Resources\PracticeStatuses\Pages;

use App\Filament\Resources\PracticeStatuses\PracticeStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeStatuses extends ListRecords
{
    protected static string $resource = PracticeStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
