<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPracticeOAMs extends ListRecords
{
    protected static string $resource = PracticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions se necessarie
        ];
    }

    public function getTitle(): string
    {
        return 'Pratiche OAM';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget se necessari
        ];
    }
}
