<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\Tables\PracticeOAMsTable;
use App\Filament\Resources\Practices\PracticeOAMResource;
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

class ListPracticeOAMs extends ListRecords
{
    protected static string $resource = PracticeOAMResource::class;

    // Definisci il gruppo di navigazione solo per questa pagina
    public static function getNavigationGroup(): ?string
    {
        return 'Compilance';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTable(): FilamentTable
    {
        return PracticeOAMsTable::configure(parent::getTable());
    }

    public function getTitle(): string
    {
        return 'Segnalazioni OAM';
    }

    public function getSubheading(): string|Htmlable|null
    {
        // $record = $this->getRecord();

        return new HtmlString('OAM Vigilanza semestrale');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget se necessari
        ];
    }
}
