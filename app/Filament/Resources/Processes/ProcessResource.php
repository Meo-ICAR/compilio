<?php

namespace App\Filament\Resources\Processes;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Processes\Pages\CreateProcess;
use App\Filament\Resources\Processes\Pages\EditProcess;
use App\Filament\Resources\Processes\Pages\ListProcess;
use App\Filament\Resources\Processes\RelationManagers\ProcessTasksRelationManager;
use App\Filament\Resources\Processes\Schemas\ProcessForm;
use App\Filament\Resources\Processes\Tables\ProcessTable;
use App\Models\Process;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;;
use UnitEnum;

class ProcessResource extends Resource
{
    protected static ?string $model = Process::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Processi Aziendali';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processi';

    protected static string|UnitEnum|null $navigationGroup = 'Processi';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return ProcessForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProcessTasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProcess::route('/'),
            'create' => CreateProcess::route('/create'),
            'edit' => EditProcess::route('/{record}/edit'),
        ];
    }
}
