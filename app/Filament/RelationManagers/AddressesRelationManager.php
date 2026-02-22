<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\Addresses\Schemas\AddressForm;
use App\Filament\Resources\Addresses\Tables\AddressesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Schema $schema): Schema
    {
        return AddressForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return AddressesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
