<?php

namespace App\Filament\Resources\OamScopes\Tables;

use App\Filament\Traits\CanExportTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OamScopesTable
{
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Codice OAM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Descrizione')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('tipo_prodotto')
                    ->label('Tipo Prodotto')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
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
