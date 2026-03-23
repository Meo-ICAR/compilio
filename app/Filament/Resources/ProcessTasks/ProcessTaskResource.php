<?php

namespace App\Filament\Resources\ProcessTasks;

use App\Filament\Resources\ProcessTasks\Pages\CreateProcessTask;
use App\Filament\Resources\ProcessTasks\Pages\EditProcessTask;
use App\Filament\Resources\ProcessTasks\Pages\ListProcessTasks;
use App\Filament\Resources\ProcessTasks\Schemas\ProcessTaskForm;
use App\Filament\Resources\ProcessTasks\Tables\ProcessTasksTable;
use App\Models\ProcessTask;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;;
use UnitEnum;

class ProcessTaskResource extends Resource
{
    protected static ?string $model = ProcessTask::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'Task';
    protected static ?string $modelLabel = 'Task';
    protected static ?string $pluralModelLabel = 'Task';
    protected static string|UnitEnum|null $navigationGroup = 'Processi';
    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return ProcessTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProcessTasksTable::configure($table);
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
            'index' => ListProcessTasks::route('/'),
            'create' => CreateProcessTask::route('/create'),
            'edit' => EditProcessTask::route('/{record}/edit'),
        ];
    }
}
