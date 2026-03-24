<?php

namespace App\Filament\Resources\OAMSoggettis;

use App\Filament\Resources\OAMSoggettis\Pages\CreateOAMSoggetti;
use App\Filament\Resources\OAMSoggettis\Pages\EditOAMSoggetti;
use App\Filament\Resources\OAMSoggettis\Pages\ListOAMSoggettis;
use App\Filament\Resources\OAMSoggettis\Schemas\OAMSoggettiForm;
use App\Filament\Resources\OAMSoggettis\Tables\OAMSoggettisTable;
use App\Models\OAMSoggetti;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class OAMSoggettiResource extends Resource
{
    protected static ?string $model = OAMSoggetti::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'OAM Soggetti';

    protected static ?string $modelLabel = 'OAM';

    protected static ?string $pluralModelLabel = 'OAM';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return OAMSoggettiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OAMSoggettisTable::configure($table);
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
            'index' => ListOAMSoggettis::route('/'),
            'create' => CreateOAMSoggetti::route('/create'),
            'edit' => EditOAMSoggetti::route('/{record}/edit'),
        ];
    }
}
