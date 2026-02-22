<?php

namespace App\Filament\Resources\SoftwareCategories;
use AppFilamentResourcesSoftwareCategoriesPagesCreateSoftwareCategory;
use AppFilamentResourcesSoftwareCategoriesPagesEditSoftwareCategory;
use AppFilamentResourcesSoftwareCategoriesPagesListSoftwareCategory;

use App\Filament\Resources\SoftwareCategories\Pages\CreateSoftwareCategory;
use App\Filament\Resources\SoftwareCategories\Pages\EditSoftwareCategory;
use App\Filament\Resources\SoftwareCategories\Pages\ListSoftwareCategories;
use App\Filament\Resources\SoftwareCategories\Schemas\SoftwareCategoryForm;
use App\Filament\Resources\SoftwareCategories\Tables\SoftwareCategoriesTable;
use App\Models\SoftwareCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SoftwareCategoryResource extends Resource
{
    protected static ?string $model = SoftwareCategory::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return SoftwareCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareCategoriesTable::configure($table);
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
            'index' => ListSoftwareCategories::route('/'),
            'create' => CreateSoftwareCategory::route('/create'),
            'edit' => EditSoftwareCategory::route('/{record}/edit'),
        ];
    }
}
