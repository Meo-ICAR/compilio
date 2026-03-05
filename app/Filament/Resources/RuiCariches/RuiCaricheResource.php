<?php

namespace App\Filament\Resources\RuiCariches;

use App\Filament\Resources\RuiCariches\Pages\CreateRuiCariche;
use App\Filament\Resources\RuiCariches\Pages\EditRuiCariche;
use App\Filament\Resources\RuiCariches\Pages\ListRuiCariches;
use App\Filament\Resources\RuiCariches\Schemas\RuiCaricheForm;
use App\Filament\Resources\RuiCariches\Tables\RuiCarichesTable;
use App\Models\RuiCariche;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RuiCaricheResource extends Resource
{
    protected static ?string $model = RuiCariche::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RuiCaricheForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiCarichesTable::configure($table);
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
            'index' => ListRuiCariches::route('/'),
            'create' => CreateRuiCariche::route('/create'),
            'edit' => EditRuiCariche::route('/{record}/edit'),
        ];
    }
}
