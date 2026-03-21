<?php

namespace App\Filament\Resources\ProcessTasks\Tables;

use App\Filament\Traits\CanExportTable;
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
            ->modifyQueryUsing(fn($query) => $query->with(['businessFunctions', 'businessFunctions.pivot']))
            ->columns([
                TextColumn::make('name')
                    ->label('Attività')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->name),
                TextColumn::make('raci_summary')
                    ->label('Matrice RACI')
                    ->formatStateUsing(fn($record) =>
                        $record
                            ->businessFunctions
                            ->groupBy(fn($f) => $f->pivot->role)
                            ->map(fn($functions, $role) => "{$role}: " . $functions->pluck('code')->implode(', '))
                            ->implode(' | '))
                    ->badge()
                    ->color(fn($record) =>
                        $record->businessFunctions->contains(fn($f) => $f->pivot->role === 'A') ? 'danger' : 'primary'),
                TextColumn::make('code')
                    ->label('Codice Dettaglio')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                TextColumn::make('sort_order')
                    ->label('Ordinamento')
                    ->sortable()
                    ->badge(),
                TextColumn::make('business_functions_count')
                    ->label('Funzioni RACI')
                    ->counts('businessFunctions')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->businessFunctions_count > 0 ? 'success' : 'gray'),
            ])
            ->defaultSort('sort_order')
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
