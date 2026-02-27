<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\Tables\PracticeOAMsTable;
use App\Filament\Resources\Practices\PracticeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table as FilamentTable;
use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;  // CORRETTO
use Illuminate\Support\HtmlString;

class ListPracticeOAMs extends ListRecords
{
    protected static string $resource = PracticeResource::class;

    public function getTable(): FilamentTable
    {
        return PracticeOAMsTable::configure(parent::getTable());
    }

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

    public function getSubheading(): string|Htmlable|null
    {
        // $record = $this->getRecord();

        return new HtmlString('OAM Vigilanza annuale');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget se necessari
        ];
    }
}
