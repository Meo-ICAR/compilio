<?php

namespace App\Filament\Resources\Coges;

use App\Filament\Resources\Coges\Pages\CreateCoge;
use App\Filament\Resources\Coges\Pages\EditCoge;
use App\Filament\Resources\Coges\Pages\ListCoges;
use App\Filament\Resources\Coges\Schemas\CogeForm;
use App\Filament\Resources\Coges\Tables\CogesTable;
use App\Models\Coge;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CogeResource extends Resource
{
    protected static ?string $model = Coge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Contabilita';

    protected static ?string $modelLabel = 'Primanota';

    protected static ?string $pluralModelLabel = 'Primenote';

    protected static string|UnitEnum|null $navigationGroup = 'Configurazioni';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return CogeForm::configure($schema);
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
            'create' => CreateCoge::route('/create'),
            'edit' => EditCoge::route('/{record}/edit'),
        ];
    }
}
