<?php

namespace App\Filament\Resources\DocumentTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('is_person')
                    ->label('Persona Fisica')
                    ->boolean(),
                IconColumn::make('is_signed')
                    ->label('Richiede Firma')
                    ->boolean(),
                IconColumn::make('is_stored')
                    ->label('Conservazione')
                    ->boolean(),
                TextColumn::make('duration')
                    ->label('Durata')
                    ->suffix(' giorni')
                    ->sortable(),
                TextColumn::make('emitted_by')
                    ->label('Ente Rilascio')
                    ->searchable(),
                IconColumn::make('is_sensible')
                    ->label('Dati Sensibili')
                    ->boolean(),
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
