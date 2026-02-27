<?php

namespace App\Filament\Resources\PracticeCommissionStatuses;

use App\Filament\Resources\PracticeCommissionStatuses\Pages\CreatePracticeCommissionStatus;
use App\Filament\Resources\PracticeCommissionStatuses\Pages\EditPracticeCommissionStatus;
use App\Filament\Resources\PracticeCommissionStatuses\Pages\ListPracticeCommissionStatuses;
use App\Filament\Resources\PracticeCommissionStatuses\Schemas\PracticeCommissionStatusForm;
use App\Filament\Resources\PracticeCommissionStatuses\Tables\PracticeCommissionStatusesTable;
use App\Models\PracticeCommissionStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PracticeCommissionStatusResource extends Resource
{
    protected static ?string $model = PracticeCommissionStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PracticeCommissionStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeCommissionStatusesTable::configure($table);
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
            'index' => ListPracticeCommissionStatuses::route('/'),
            'create' => CreatePracticeCommissionStatus::route('/create'),
            'edit' => EditPracticeCommissionStatus::route('/{record}/edit'),
        ];
    }
}
