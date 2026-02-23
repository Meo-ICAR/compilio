<?php

namespace App\Filament\Resources\CompilanceViolations;

use App\Filament\Resources\CompilanceViolations\Pages\CreateCompilanceViolation;
use App\Filament\Resources\CompilanceViolations\Pages\EditCompilanceViolation;
use App\Filament\Resources\CompilanceViolations\Pages\ListCompilanceViolations;
use App\Filament\Resources\CompilanceViolations\Schemas\CompilanceViolationForm;
use App\Filament\Resources\CompilanceViolations\Tables\CompilanceViolationsTable;
use App\Models\ComplianceViolation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CompilanceViolationResource extends Resource
{
    protected static ?string $model = ComplianceViolation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Violazioni';

    protected static ?int $navigationSort = 9;

    protected static ?string $modelLabel = 'Registro Violazione';

    protected static ?string $pluralModelLabel = 'Registro Violazioni';

    protected static string|UnitEnum|null $navigationGroup = 'Segnalazioni';

    public static function form(Schema $schema): Schema
    {
        return CompilanceViolationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompilanceViolationsTable::configure($table);
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
            'index' => ListCompilanceViolations::route('/'),
            'create' => CreateCompilanceViolation::route('/create'),
            'edit' => EditCompilanceViolation::route('/{record}/edit'),
        ];
    }
}
