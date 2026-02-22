<?php

namespace App\Filament\Resources\Proformas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProformasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agent_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('commission_label')
                    ->searchable(),
                TextColumn::make('total_commissions')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('enasarco_retained')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remburse')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remburse_label')
                    ->searchable(),
                TextColumn::make('contribute')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contribute_label')
                    ->searchable(),
                TextColumn::make('refuse')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('refuse_label')
                    ->searchable(),
                TextColumn::make('net_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('month')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('updated_at')
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
