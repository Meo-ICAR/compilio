<?php

namespace App\Filament\Resources\SoftwareApplications;

use App\Filament\Resources\SoftwareApplications\Pages\CreateSoftwareApplication;
use App\Filament\Resources\SoftwareApplications\Pages\EditSoftwareApplication;
use App\Filament\Resources\SoftwareApplications\Pages\ListSoftwareApplications;
use App\Filament\Resources\SoftwareApplications\Schemas\SoftwareApplicationForm;
use App\Filament\Resources\SoftwareApplications\Tables\SoftwareApplicationsTable;
use App\Models\SoftwareApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SoftwareApplicationResource extends Resource
{
    protected static ?string $model = SoftwareApplication::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return SoftwareApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareApplicationsTable::configure($table);
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
            'index' => ListSoftwareApplications::route('/'),
            'create' => CreateSoftwareApplication::route('/create'),
            'edit' => EditSoftwareApplication::route('/{record}/edit'),
        ];
    }
}
