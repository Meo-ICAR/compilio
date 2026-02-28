<?php

namespace App\Filament\Resources\Practices;

use App\Filament\Resources\Practices\Pages\ListPracticeOAMs;
use App\Filament\Resources\Practices\Tables\PracticeOAMsTable;
use App\Models\Practice;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PracticeOAMResource extends Resource
{
    protected static ?string $model = Practice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Pratiche OAM';

    protected static ?string $modelLabel = 'Pratica OAM';

    protected static ?string $pluralModelLabel = 'Pratiche OAM';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = true;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return PracticeOAMsTable::configure($table);
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
            'index' => ListPracticeOAMs::route('/'),
        ];
    }
}
