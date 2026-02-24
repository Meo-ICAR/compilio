<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyClients\Tables\CompanyClientsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompanyClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'companyClients';

    protected static ?string $modelLabel = 'Cliente Aziendale';

    protected static ?string $pluralModelLabel = 'Clienti Aziendali';

    protected static ?string $title = 'Clienti Aziendali';

    public function table(Table $table): Table
    {
        return CompanyClientsTable::configure($table);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }
}
