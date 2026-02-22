<?php

namespace App\Filament\Resources\Addresses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Indirizzo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('street')
                    ->label('Via')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Città')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('zip_code')
                    ->label('CAP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address_type_id')
                    ->label('Tipo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data Creazione')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Data Aggiornamento')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
