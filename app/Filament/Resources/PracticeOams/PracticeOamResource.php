<?php

namespace App\Filament\Resources\PracticeOams;

use App\Filament\Resources\PracticeOams\Pages\CreatePracticeOam;
use App\Filament\Resources\PracticeOams\Pages\EditPracticeOam;
use App\Filament\Resources\PracticeOams\Pages\ListPracticeOams;
use App\Filament\Resources\PracticeOams\Schemas\PracticeOamForm;
use App\Filament\Resources\PracticeOams\Tables\PracticeOamsTable;
use App\Models\PracticeOam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PracticeOamResource extends Resource
{
    protected static ?string $model = PracticeOam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PracticeOamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeOamsTable::configure($table);
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
            'index' => ListPracticeOams::route('/'),
            'create' => CreatePracticeOam::route('/create'),
            'edit' => EditPracticeOam::route('/{record}/edit'),
        ];
    }
}
