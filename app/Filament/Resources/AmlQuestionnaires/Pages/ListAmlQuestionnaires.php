<?php

namespace App\Filament\Resources\AmlQuestionnaires\Pages;

use App\Filament\Resources\AmlQuestionnaires\AmlQuestionnaireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAmlQuestionnaires extends ListRecords
{
    protected static string $resource = AmlQuestionnaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
