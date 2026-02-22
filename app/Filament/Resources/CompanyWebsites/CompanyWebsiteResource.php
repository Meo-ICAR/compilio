<?php

namespace App\Filament\Resources\CompanyWebsites;

use App\Filament\Resources\CompanyWebsites\Pages\CreateCompanyWebsite;
use App\Filament\Resources\CompanyWebsites\Pages\EditCompanyWebsite;
use App\Filament\Resources\CompanyWebsites\Pages\ListCompanyWebsites;
use App\Filament\Resources\CompanyWebsites\Schemas\CompanyWebsiteForm;
use App\Filament\Resources\CompanyWebsites\Tables\CompanyWebsitesTable;
use App\Models\CompanyWebsite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CompanyWebsiteResource extends Resource
{
    protected static ?string $model = CompanyWebsite::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'Impostazioni';

    public static function form(Schema $schema): Schema
    {
        return CompanyWebsiteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyWebsitesTable::configure($table);
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
            'index' => ListCompanyWebsites::route('/'),
            'create' => CreateCompanyWebsite::route('/create'),
            'edit' => EditCompanyWebsite::route('/{record}/edit'),
        ];
    }
}
