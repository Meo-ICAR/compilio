<?php

namespace App\Filament\Resources\ProcessTasks\Tables;

use App\Filament\Traits\CanExportTable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProcessTasksTable
{
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['businessFunctions']))
            ->columns([
                TextColumn::make('name')
                    ->label('Attività')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->name),
                // --- COLONNE MATRICE RACI ---
                // R - Responsible (Chi esegue)
                TextColumn::make('responsible')
                    ->label('R')
                    ->tooltip("Responsible: Chi esegue l'attività")
                    ->state(fn($record) =>
                        $record->businessFunctions->where('pivot.role', 'R')->pluck('code')->toArray())
                    ->badge()
                    ->color('gray')
                    ->separator(','),
                // A - Accountable (Chi approva/risponde)
                TextColumn::make('accountable')
                    ->label('A')
                    ->tooltip('Accountable: Chi ha la responsabilità ultima e approva')
                    ->state(fn($record) =>
                        $record->businessFunctions->where('pivot.role', 'A')->pluck('code')->toArray())
                    ->badge()
                    ->color('danger')
                    ->separator(','),
                // C - Consulted (Chi aiuta)
                TextColumn::make('consulted')
                    ->label('C')
                    ->tooltip('Consulted: Chi deve essere consultato (scambio bidirezionale)')
                    ->state(fn($record) =>
                        $record->businessFunctions->where('pivot.role', 'C')->pluck('code')->toArray())
                    ->badge()
                    ->color('info')
                    ->separator(','),
                // I - Informed (Chi riceve info)
                TextColumn::make('informed')
                    ->label('I')
                    ->tooltip('Informed: Chi deve essere informato (scambio unidirezionale)')
                    ->state(fn($record) =>
                        $record->businessFunctions->where('pivot.role', 'I')->pluck('code')->toArray())
                    ->badge()
                    ->color('success')
                    ->separator(','),
                TextColumn::make('checklist_items_count')
                    ->label('Step Checklist')
                    ->counts('checklistItems')
                    ->badge()
                    ->color('warning'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('function')
                    ->relationship('businessFunctions', 'code')
                    ->label('Filtra per Funzione'),
                SelectFilter::make('taskable_type')
                    ->label('Tipo Entità')
                    ->options([
                        'App\Models\PracticeScope' => 'Ambito Pratica (Prodotto)',
                        'App\Models\Company' => 'Azienda',
                        'App\Models\Client' => 'Cliente',
                        'App\Models\Project' => 'Progetto',
                        'App\Models\Process' => 'Processo',
                    ]),
                SelectFilter::make('has_accountable')
                    ->label('Ha Accountable')
                    ->options([
                        'yes' => 'Sì',
                        'no' => 'No',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'yes') {
                            $query->whereHas('businessFunctions', fn($q) => $q->where('role', 'A'));
                        } elseif ($data['value'] === 'no') {
                            $query->whereDoesntHave('businessFunctions', fn($q) => $q->where('role', 'A'));
                        }
                    }),
                SelectFilter::make('has_raci')
                    ->label('Ha Assegnazioni RACI')
                    ->options([
                        'yes' => 'Sì',
                        'no' => 'No',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'yes') {
                            $query->whereHas('businessFunctions');
                        } elseif ($data['value'] === 'no') {
                            $query->whereDoesntHave('businessFunctions');
                        }
                    }),
            ])
            ->actions([
                Action::make('view_checklist')
                    ->label('Checklist')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->modalHeading(fn($record) => 'Checklist per: ' . $record->name)
                    ->modalContent(fn($record) => view('filament.components.checklist-preview', [
                        'items' => $record->checklistItems()->orderBy('ordine')->get()
                    ])),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                EditAction::make('create')
                    ->label('Crea Nuova Attività')
                    ->url(fn() => static::getUrl('create')),
            ]);
    }
}
