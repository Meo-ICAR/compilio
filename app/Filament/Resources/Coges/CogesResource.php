<?php

namespace App\Filament\Resources\Coge;

use App\Filament\Resources\Coge\Pages\CreateCoges;
use App\Filament\Resources\Coge\Pages\EditCoges;
use App\Filament\Resources\Coge\Pages\ListCoges;
use App\Filament\Resources\Coge\Schemas\CogesForm;
use App\Filament\Resources\Coge\Tables\CogesTable;
use App\Models\Coge;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CogesResource extends Resource
{
    protected static ?string $model = Coges::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Contabilità';

    protected static ?string $modelLabel = 'Contabilità';

    protected static ?string $pluralModelLabel = 'Contabilità';

    protected static string|UnitEnum|null $navigationGroup = 'Configurazione';

    public static function form(Schema $schema): Schema
    {
        return CogesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CogesTable::configure($table);
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
            'index' => ListCoges::route('/'),
            'create' => CreateCoges::route('/create'),
            'edit' => EditCoges::route('/{record}/edit'),
        ];
    }
}
