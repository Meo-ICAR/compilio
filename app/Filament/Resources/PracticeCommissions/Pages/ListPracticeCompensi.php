<?php

namespace App\Filament\Resources\PracticeCommissions\Pages;

use App\Filament\Resources\PracticeCommissions\Tables\PracticeCompensiTable;
use App\Filament\Resources\PracticeCommissions\PracticeCommissionResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table as FilamentTable;
use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;  // CORRETTO
use Illuminate\Support\HtmlString;
use UnitEnum;

class ListPracticeCompensi extends ListRecords
{
    protected static string $resource = PracticeCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTable(): FilamentTable
    {
        return PracticeCompensiTable::configure(parent::getTable());
    }

    // Definisci il gruppo di navigazione solo per questa pagina
    public static function getNavigationGroup(): ?string
    {
        return 'Pratiche';
    }

    public function getTitle(): string
    {
        return 'Compensi';
    }

    public function getSubheading(): string|Htmlable|null
    {
        // $record = $this->getRecord();

        return new HtmlString('Provvigioni Attive');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget se necessari
        ];
    }
}
