<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documentType.name')
                    ->label('Tipo Documento')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nome Documento')
                    ->searchable(),
                IconColumn::make('is_template')
                    ->label('Template')
                    ->boolean(),
                //  ->helperText('Fornito da noi'),
                TextColumn::make('status')
                    ->label('Stato')
                    ->searchable()
                    ->badge(),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable()
                    ->label('Scadenza'),
                TextColumn::make('emitted_at')
                    ->date()
                    ->sortable()
                    ->label('Emissione'),
                TextColumn::make('docnumber')
                    ->searchable()
                    ->label('Numero documento'),
                TextColumn::make('emitted_by')
                    ->searchable()
                    ->label('Ente rilascio'),
                IconColumn::make('is_signed')
                    ->boolean()
                    ->label('Firmato'),
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
