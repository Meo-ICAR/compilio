<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\Tables\PracticeOAMsTable;
use App\Filament\Resources\Practices\PracticeOAMResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table as FilamentTable;
use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;  // CORRETTO
use Illuminate\Support\HtmlString;
use UnitEnum;

class ListPracticeOAMs extends ListRecords
{
    protected static string $resource = PracticeOAMResource::class;

    // Definisci il gruppo di navigazione solo per questa pagina
    public static function getNavigationGroup(): ?string
    {
        return 'Compilance';
    }

    // Definisci l'etichetta che apparirà nel menu
    public static function getNavigationLabel(): string
    {
        return 'Nuovo Utente';
    }

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
