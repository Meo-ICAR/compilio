<?php

namespace App\Filament\Resources\PracticeScopes\Pages;

use App\Filament\Resources\PracticeScopes\PracticeScopeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeScopes extends ListRecords
{
    protected static string $resource = PracticeScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
