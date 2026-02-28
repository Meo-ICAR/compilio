<?php

namespace App\Filament\Resources\Principals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class PrincipalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('principal_type')
                    ->label('Tipo Mandante')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'no' => 'Non Specificato',
                        'banca' => 'Banca',
                        'assicurazione' => 'Assicurazione',
                        'agente' => 'Agente',
                        'agente_captive' => 'Agente Captive',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'no' => 'gray',
                        'banca' => 'blue',
                        'assicurazione' => 'green',
                        'agente' => 'purple',
                        'agente_captive' => 'orange',
                        default => 'gray',
                    }),
                TextColumn::make('abi')
                    ->searchable(),
                TextColumn::make('stipulated_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('dismissed_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->searchable(),
                TextColumn::make('vat_name')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('oam')
                    ->searchable(),
                TextColumn::make('ivass')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mandate_number')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_exclusive')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge(),
                IconColumn::make('is_dummy')
                    ->label('Fittizio')
                    ->boolean()
                    ->trueIcon('heroicon-s-exclamation-triangle')
                    ->falseIcon('heroicon-o-building-office')
                    ->color(fn($state) => $state ? 'warning' : 'success')
                    ->tooltip(fn($record) => $record->is_dummy ? 'Mandante fittizio / non convenzionato' : 'Mandante convenzionato'),
            ])
            ->filters([
                SelectFilter::make('principal_type')
                    ->label('Tipo Mandante')
                    ->options([
                        'no' => 'Non Specificato',
                        'banca' => 'Banca',
                        'assicurazione' => 'Compagnia Assicurativa',
                        'agente' => 'Agente',
                        'agente_captive' => 'Agente Captive',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                \Filament\Actions\ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(\App\Filament\Imports\PrincipalsImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
