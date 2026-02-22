<?php

namespace App\Filament\Resources\Principals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
                \Filament\Actions\ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(\App\Filament\Imports\PrincipalsImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
