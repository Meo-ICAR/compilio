<?php

namespace App\Filament\Resources\BusinessFunctions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BusinessFunctionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Codice')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('name')
                    ->label('Funzione')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('macro_area')
                    ->label('Macro Area')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Governance' => 'danger',
                        'Business / Commerciale' => 'success',
                        'Supporto' => 'warning',
                        'Controlli (II Livello)' => 'info',
                        'Controlli (III Livello)' => 'purple',
                        'Controlli / Privacy' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Strategica' => 'danger',
                        'Operativa' => 'success',
                        'Supporto' => 'warning',
                        'Controllo' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('outsourcable_status')
                    ->label('Esternalizzazione')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'si' => 'Sì',
                        'no' => 'No',
                        'parziale' => 'Parziale',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'si' => 'success',
                        'no' => 'danger',
                        'parziale' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('macro_area')
                    ->label('Macro Area')
                    ->options([
                        'Governance' => 'Governance',
                        'Business / Commerciale' => 'Business / Commerciale',
                        'Supporto' => 'Supporto',
                        'Controlli (II Livello)' => 'Controlli (II Livello)',
                        'Controlli (III Livello)' => 'Controlli (III Livello)',
                        'Controlli / Privacy' => 'Controlli / Privacy',
                    ]),
                SelectFilter::make('type')
                    ->label('Tipo Funzione')
                    ->options([
                        'Strategica' => 'Strategica',
                        'Operativa' => 'Operativa',
                        'Supporto' => 'Supporto',
                        'Controllo' => 'Controllo',
                    ]),
                SelectFilter::make('outsourcable_status')
                    ->label('Esternalizzazione')
                    ->options([
                        'no' => 'Non Esternalizzabile',
                        'si' => 'Esternalizzabile',
                        'parziale' => 'Parzialmente Esternalizzabile',
                    ]),
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
