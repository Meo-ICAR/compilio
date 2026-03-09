<?php

namespace App\Filament\Resources\SosReports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SosReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codice_protocollo_interno')
                    ->label('Protocollo')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Azienda')
                    ->searchable()
                    ->placeholder('Nessuna azienda'),
                TextColumn::make('stato')
                    ->label('Stato')
                    ->badge()
                    ->color(fn($record): string => $record->stato_color)
                    ->formatStateUsing(fn($record): string => $record->stato_label),
                TextColumn::make('grado_sospetto')
                    ->label('Grado Sospetto')
                    ->badge()
                    ->color(fn($record): string => $record->grado_sospetto_color)
                    ->formatStateUsing(fn($record): string => $record->grado_sospetto_label),
                TextColumn::make('motivo_sospetto')
                    ->label('Motivo')
                    ->limitWords(10)
                    ->searchable(),
                TextColumn::make('data_segnalazione_uif')
                    ->label('Data Segnalazione UIF')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Non segnalata'),
                TextColumn::make('protocollo_uif')
                    ->label('Protocollo UIF')
                    ->searchable()
                    ->placeholder('Nessuno'),
                TextColumn::make('responsabile.name')
                    ->label('Responsabile')
                    ->searchable()
                    ->placeholder('Non assegnato'),
                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('stato')
                    ->label('Stato')
                    ->options([
                        'istruttoria' => 'Istruttoria',
                        'archiviata' => 'Archiviata',
                        'segnalata_uif' => 'Segnalata UIF',
                    ]),
                SelectFilter::make('grado_sospetto')
                    ->label('Grado Sospetto')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
