<?php

namespace App\Filament\Resources\Processes;

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

    protected static ?string $navigationLabel = 'Processi';

    protected static ?string $modelLabel = 'Processo';

    protected static ?string $pluralModelLabel = 'Processi';

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
}
