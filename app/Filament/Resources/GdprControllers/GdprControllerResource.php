<?php

namespace App\Filament\Resources\GdprControllers;

use App\Filament\Resources\GdprControllers\Pages\CreateGdprController;
use App\Filament\Resources\GdprControllers\Pages\EditGdprController;
use App\Filament\Resources\GdprControllers\Pages\ListGdprControllers;
use App\Filament\Resources\GdprControllers\Schemas\GdprControllerForm;
use App\Filament\Resources\GdprControllers\Tables\GdprControllersTable;
use App\Models\GdprController;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;
use UnitEnum;

class GdprControllerResource extends Resource
{
    protected static ?string $model = GdprController::class;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Registro Privacy';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Registro Trattamenti';

    protected static ?string $pluralModelLabel = 'Registri Privacy';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GdprControllerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GdprControllersTable::configure($table);
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
            'index' => ListGdprControllers::route('/'),
            'create' => CreateGdprController::route('/create'),
            'edit' => EditGdprController::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
