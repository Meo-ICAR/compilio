<?php

namespace App\Filament\Resources\Comunes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComunesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codice_regione')
                    ->searchable(),
                TextColumn::make('codice_unita_territoriale')
                    ->searchable(),
                TextColumn::make('codice_provincia_storico')
                    ->searchable(),
                TextColumn::make('progressivo_comune')
                    ->searchable(),
                TextColumn::make('codice_comune_alfanumerico')
                    ->searchable(),
                TextColumn::make('denominazione')
                    ->searchable(),
                TextColumn::make('denominazione_italiano')
                    ->searchable(),
                TextColumn::make('denominazione_altra_lingua')
                    ->searchable(),
                TextColumn::make('codice_ripartizione_geografica')
                    ->searchable(),
                TextColumn::make('ripartizione_geografica')
                    ->searchable(),
                TextColumn::make('denominazione_regione')
                    ->searchable(),
                TextColumn::make('denominazione_unita_territoriale')
                    ->searchable(),
                TextColumn::make('tipologia_unita_territoriale')
                    ->searchable(),
                IconColumn::make('capoluogo_provincia')
                    ->boolean(),
                TextColumn::make('sigla_automobilistica')
                    ->searchable(),
                TextColumn::make('codice_comune_numerico')
                    ->searchable(),
                TextColumn::make('codice_comune_110_province')
                    ->searchable(),
                TextColumn::make('codice_comune_107_province')
                    ->searchable(),
                TextColumn::make('codice_comune_103_province')
                    ->searchable(),
                TextColumn::make('codice_catastale')
                    ->searchable(),
                TextColumn::make('codice_nuts1_2021')
                    ->searchable(),
                TextColumn::make('codice_nuts2_2021')
                    ->searchable(),
                TextColumn::make('codice_nuts3_2021')
                    ->searchable(),
                TextColumn::make('codice_nuts1_2024')
                    ->searchable(),
                TextColumn::make('codice_nuts2_2024')
                    ->searchable(),
                TextColumn::make('codice_nuts3_2024')
                    ->searchable(),
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
