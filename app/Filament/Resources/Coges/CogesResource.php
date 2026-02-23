<?php

namespace App\Filament\Resources\Coges;

use App\Filament\Resources\Coges\Pages\CreateCoges;
use App\Filament\Resources\Coges\Pages\EditCoges;
use App\Filament\Resources\Coges\Pages\ListCoges;
use App\Filament\Resources\Coges\Schemas\CogesForm;
use App\Filament\Resources\Coges\Tables\CogesTable;
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

    protected static string|UnitEnum|null $navigationGroup = 'Configurazione';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Contabilità';

    protected static ?string $modelLabel = 'Contabilità';

    protected static ?string $pluralModelLabel = 'Contabilità';

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
