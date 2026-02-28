<?php

namespace App\Filament\Resources\ClientMandates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClientMandatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->searchable(),
                TextColumn::make('numero_mandato')
                    ->searchable(),
                TextColumn::make('data_firma_mandato')
                    ->date()
                    ->sortable(),
                TextColumn::make('data_scadenza_mandato')
                    ->date()
                    ->sortable(),
                TextColumn::make('importo_richiesto_mandato')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('scopo_finanziamento')
                    ->searchable(),
                TextColumn::make('data_consegna_trasparenza')
                    ->date()
                    ->sortable(),
                TextColumn::make('stato')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
