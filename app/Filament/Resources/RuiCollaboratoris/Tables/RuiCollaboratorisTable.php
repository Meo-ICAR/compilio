<?php

namespace App\Filament\Resources\RuiCollaboratoris\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuiCollaboratorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('oss')
                    ->searchable(),
                TextColumn::make('livello')
                    ->searchable(),
                TextColumn::make('num_iscr_intermediario')
                    ->searchable(),
                TextColumn::make('num_iscr_collaboratori_i_liv')
                    ->searchable(),
                TextColumn::make('num_iscr_collaboratori_ii_liv')
                    ->searchable(),
                TextColumn::make('qualifica_rapporto')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
