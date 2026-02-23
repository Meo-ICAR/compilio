<?php

namespace App\Filament\Resources\PracticeCommissions;

use App\Filament\Resources\PracticeCommissions\Pages\CreatePracticeCommission;
use App\Filament\Resources\PracticeCommissions\Pages\EditPracticeCommission;
use App\Filament\Resources\PracticeCommissions\Pages\ListPracticeCommissions;
use App\Filament\Resources\PracticeCommissions\Schemas\PracticeCommissionForm;
use App\Filament\Resources\PracticeCommissions\Tables\PracticeCommissionsTable;
use App\Models\PracticeCommission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PracticeCommissionResource extends Resource
{
    protected static ?string $model = PracticeCommission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Provvigioni';

    protected static ?string $modelLabel = 'Provvigione';

    protected static ?string $pluralModelLabel = 'Provvigioni';

    protected static string|UnitEnum|null $navigationGroup = 'Pratiche';

    public static function form(Schema $schema): Schema
    {
        return PracticeCommissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeCommissionsTable::configure($table);
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
            'index' => ListPracticeCommissions::route('/'),
            'create' => CreatePracticeCommission::route('/create'),
            'edit' => EditPracticeCommission::route('/{record}/edit'),
        ];
    }
}
