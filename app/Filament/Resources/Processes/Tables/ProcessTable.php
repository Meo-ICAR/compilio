<?php

namespace App\Filament\Resources\Processes\Tables;

use App\Filament\Traits\CanExportTable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProcessTable
{
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Processo')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->name),
                TextColumn::make('process_tasks_count')
                    ->label('Task')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->process_tasks_count > 0 ? 'success' : 'gray')
                    ->suffix(' task'),
                TextColumn::make('groupcode')
                    ->label('Codice Gruppo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                TextColumn::make('periodicity_label')
                    ->label('Periodicità')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => match ($record->periodicity) {
                        'once' => 'gray',
                        'monthly' => 'blue',
                        'quarterly' => 'green',
                        'semiannual' => 'orange',
                        'annual' => 'purple',
                        default => 'gray',
                    }),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('periodicity')
                    ->label('Periodicità')
                    ->options([
                        'once' => 'Una Tantum',
                        'monthly' => 'Mensile',
                        'quarterly' => 'Trimestrale',
                        'semiannual' => 'Semestrale',
                        'annual' => 'Annuale',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->placeholder('Tutti')
                    ->trueLabel('Solo attivi')
                    ->falseLabel('Solo inattivi'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
