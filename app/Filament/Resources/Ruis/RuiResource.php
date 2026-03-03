<?php

namespace App\Filament\Resources\Ruis;

use App\Filament\Resources\Ruis\Pages\CreateRui;
use App\Filament\Resources\Ruis\Pages\EditRui;
use App\Filament\Resources\Ruis\Pages\ListRuis;
use App\Filament\Resources\Ruis\Schemas\RuiForm;
use App\Filament\Resources\Ruis\Tables\RuisTable;
use App\Models\Rui;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RuiResource extends Resource
{
    protected static ?string $model = Rui::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RuiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuisTable::configure($table);
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
            'index' => ListRuis::route('/'),
            'create' => CreateRui::route('/create'),
            'edit' => EditRui::route('/{record}/edit'),
        ];
    }
}
