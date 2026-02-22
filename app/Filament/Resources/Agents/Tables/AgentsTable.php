<?php

namespace App\Filament\Resources\Agents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AgentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('oam')
                    ->searchable(),
                TextColumn::make('oam_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('oam_name')
                    ->searchable(),
                TextColumn::make('stipulated_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('dismissed_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('contribute')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contributeFrequency')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contributeFrom')
                    ->date()
                    ->sortable(),
                TextColumn::make('remburse')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->searchable(),
                TextColumn::make('vat_name')
                    ->searchable(),
                IconColumn::make('is_active')
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
