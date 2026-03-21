<?php

namespace App\Filament\Resources\RaciAssignements\Tables;

use App\Filament\Traits\CanExportTable;
use App\Models\BusinessFunction;
use App\Models\ProcessTask;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RaciAssignementsTable
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
                TextColumn::make('processTask.name')
                    ->label('Attività')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->processTask->name),
                TextColumn::make('businessFunction.code')
                    ->label('Funzione')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('businessFunction.name')
                    ->label('Nome Funzione')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->businessFunction->name),
                TextColumn::make('role')
                    ->label('Ruolo RACI')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'R' => 'warning',
                        'A' => 'danger',
                        'C' => 'info',
                        'I' => 'gray',
                        default => 'primary'
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'R' => 'R - Responsible',
                        'A' => 'A - Accountable',
                        'C' => 'C - Consulted',
                        'I' => 'I - Informed',
                        default => $state
                    })
                    ->sortable(),
            ])
            ->defaultSort('processTask.name', 'asc')
            ->filters([
                SelectFilter::make('taskable_type')
                    ->label('Tipo Entità')
                    ->options([
                        'App\Models\PracticeScope' => 'Ambito Pratica (Prodotto)',
                        'App\Models\Company' => 'Azienda',
                        'App\Models\Client' => 'Cliente',
                        'App\Models\Project' => 'Progetto',
                        'App\Models\Process' => 'Processo',
                    ]),
                SelectFilter::make('role')
                    ->label('Ruolo RACI')
                    ->options([
                        'R' => 'R - Responsible',
                        'A' => 'A - Accountable',
                        'C' => 'C - Consulted',
                        'I' => 'I - Informed',
                    ])
                    ->default('A'),
                SelectFilter::make('business_function')
                    ->relationship('businessFunction', 'name')
                    ->searchable()
                    ->label('Funzione'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                EditAction::make('create')
                    ->label('Crea Nuova Assegnazione RACI')
                    ->url(fn() => static::getUrl('create')),
            ]);
    }
}
