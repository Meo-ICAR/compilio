<?php

namespace App\Filament\Resources\Principals\Pages;

use App\Filament\Resources\Principals\Tables\PrincipalScopesTable;
use App\Filament\Resources\Principals\PrincipalResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Contracts\Support\Htmlable;  // CORRETTO
use Illuminate\Support\HtmlString;
use UnitEnum;

class ListPrincipalScopes extends ListRecords
{
    protected static string $resource = PrincipalResource::class;

    public function getTable(): Table
    {
        return PrincipalScopesTable::configure(parent::getTable());
    }

    // Definisci il gruppo di navigazione solo per questa pagina
    public static function getNavigationGroup(): ?string
    {
        return 'Convenzioni';
    }

    public function getTitle(): string
    {
        return 'Convenzioni';
    }
}
