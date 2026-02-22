<?php

namespace App\Filament\Resources\TrainingTemplates\Pages;

use App\Filament\Resources\TrainingTemplates\TrainingTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainingTemplates extends ListRecords
{
    protected static string $resource = TrainingTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
