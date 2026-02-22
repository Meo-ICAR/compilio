<?php

namespace App\Filament\Resources\Mandates;

use App\Filament\Resources\Mandates\Pages\CreateMandate;
use App\Filament\Resources\Mandates\Pages\EditMandate;
use App\Filament\Resources\Mandates\Pages\ListMandates;
use App\Filament\Resources\Mandates\Schemas\MandateForm;
use App\Filament\Resources\Mandates\Tables\MandatesTable;
use App\Models\Mandate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MandateResource extends Resource
{
    protected static ?string $model = Mandate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Nucleo Centrale';


    public static function form(Schema $schema): Schema
    {
        return MandateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MandatesTable::configure($table);
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
            'index' => ListMandates::route('/'),
            'create' => CreateMandate::route('/create'),
            'edit' => EditMandate::route('/{record}/edit'),
        ];
    }
}
