<?php

namespace App\Filament\Resources\Checklists\Tables;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables;

class ChecklistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('type')
                    ->label('Tipo')
                    ->colors([
                        'primary' => 'loan_management',
                        'warning' => 'audit',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'loan_management' => 'Gestione Prestiti',
                        'audit' => 'Audit/Compliance',
                        default => $state,
                    }),
                TextColumn::make('principal.name')
                    ->label('Principal')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessuno'),
                IconColumn::make('is_practice')
                    ->label('Pratiche')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_audit')
                    ->label('Audit')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('checklist_items_count')
                    ->label('Elementi')
                    ->counts('checklistItems')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'loan_management' => 'Gestione Prestiti',
                        'audit' => 'Audit/Compliance',
                    ]),
                TernaryFilter::make('is_practice')
                    ->label('Riferito a Pratiche'),
                TernaryFilter::make('is_audit')
                    ->label('Per Audit'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
