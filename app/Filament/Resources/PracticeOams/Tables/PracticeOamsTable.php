<?php

namespace App\Filament\Resources\PracticeOams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PracticeOamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_id')
                    ->searchable(),
                TextColumn::make('practice_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('oam_code_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compenso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compenso_lavorazione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compenso_premio')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compenso_rimborso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compenso_assicurazione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('compenso_cliente')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('storno')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provvigione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provvigione_lavorazione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provvigione_premio')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provvigione_rimborso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provvigione_assicurazione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('provvigione_storno')
                    ->numeric()
                    ->sortable(),
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
