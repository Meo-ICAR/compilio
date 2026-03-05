<?php

namespace App\Filament\Resources\RuiCollaboratoris\Pages;

use App\Filament\Resources\RuiCollaboratoris\RuiCollaboratoriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiCollaboratoris extends ListRecords
{
    protected static string $resource = RuiCollaboratoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
