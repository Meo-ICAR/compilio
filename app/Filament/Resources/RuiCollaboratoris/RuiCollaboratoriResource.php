<?php

namespace App\Filament\Resources\RuiCollaboratoris;

use App\Filament\Resources\RuiCollaboratoris\Pages\CreateRuiCollaboratori;
use App\Filament\Resources\RuiCollaboratoris\Pages\EditRuiCollaboratori;
use App\Filament\Resources\RuiCollaboratoris\Pages\ListRuiCollaboratoris;
use App\Filament\Resources\RuiCollaboratoris\Schemas\RuiCollaboratoriForm;
use App\Filament\Resources\RuiCollaboratoris\Tables\RuiCollaboratorisTable;
use App\Models\RuiCollaboratori;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RuiCollaboratoriResource extends Resource
{
    protected static ?string $model = RuiCollaboratori::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RuiCollaboratoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiCollaboratorisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRuiCollaboratoris::route('/'),
            'create' => CreateRuiCollaboratori::route('/create'),
            'edit' => EditRuiCollaboratori::route('/{record}/edit'),
        ];
    }
}
