<?php

namespace App\Filament\Resources\PracticeOams\Tables;

use App\Models\PracticeOam;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class PracticeOamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([25, 50, 100, 'all'])
            ->reorderableColumns()
            ->selectable()
            ->groups([
                Group::make('oam_code')
                    ->label('OAM')
                    ->collapsible(),  // SOSTITUISCE le vecchie impostazioni di groupingSettings
                Group::make('tipo_prodotto')
                    ->label('Prodotto')
                    ->collapsible(),  // SOSTITUISCE le vecchie impostazioni di groupingSettings
            ])
            ->collapsedGroupsByDefault()
            ->columns([
                TextColumn::make('practice.scopeOAM.oam_code')
                    ->label('OAM Code')
                    ->sortable(),
                TextColumn::make('practice.scopeOAM.tipo_prodotto')
                    ->label('OAM Tipo')
                    ->sortable(),
                TextColumn::make('tipo_prodotto')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_conventioned')
                    ->label('Convenzionata')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                IconColumn::make('is_notconventioned')
                    ->label('NON Convenz.')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                IconColumn::make('is_perfected')
                    ->label('Perfezionata')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                IconColumn::make('is_working')
                    ->label('Lavorazione')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Mandante')
                    ->sortable(),
                TextColumn::make('practice.principal.type')
                    ->label('Tipo fin.')
                    ->sortable(),
                TextColumn::make('practice.inserted_at')
                    ->label('Inserita')
                    ->date()
                    ->sortable(),
                TextColumn::make('practice.perfected_at')
                    ->label('Perfezionata')
                    ->date()
                    ->sortable(),
                TextColumn::make('practice.CRM_code')
                    ->label('Codice')
                    ->sortable(),
                TextColumn::make('practice.name')
                    ->label('Pratica')
                    ->sortable(),
                TextColumn::make('erogato')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_lavorazione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_premio')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_rimborso')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_assicurazione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_cliente')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('storno')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_lavorazione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_premio')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_rimborso')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_assicurazione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_storno')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('is_conventioned')
                    ->label('Convenzionata')
                    ->query(fn($query) => $query->where('is_conventioned', true)),
                Filter::make('is_notconventioned')
                    ->label('NON Convenzionata')
                    ->query(fn($query) => $query->where('is_conventioned', true)),
                Filter::make('is_perfected')
                    ->label('Perfezionata')
                    ->query(fn($query) => $query->where('is_perfected', true)),
                Filter::make('is_working')
                    ->label('Lavorazione')
                    ->query(fn($query) => $query->where('is_working', true)),
                SelectFilter::make('practice.scopeOAM.oam_code')
                    ->label('Filtra per Tipo')
                    ->multiple()  // Abilita la selezione multipla
                    ->options(
                        // Recupera i valori unici della colonna 'type' dal database
                        fn() => PracticeOam::query()
                            ->pluck('tipo_prodotto', 'tipo_prodotto')  // 'valore' => 'etichetta'
                            ->sort()
                            ->toArray()
                    )
                    ->searchable(),  // Opzionale: aggiunge una barra di ricerca nel dropdown
                SelectFilter::make('tipo_prodotto')
                    ->label('Filtra per Tipo')
                    ->multiple()  // Abilita la selezione multipla
                    ->options(
                        // Recupera i valori unici della colonna 'type' dal database
                        fn() => PracticeOam::query()
                            ->pluck('tipo_prodotto', 'tipo_prodotto')  // 'valore' => 'etichetta'
                            ->sort()
                            ->toArray()
                    )
                    ->searchable(),  // Opzionale: aggiunge una barra di ricerca nel dropdown
                SelectFilter::make('name')
                    ->label('Mandante')
                    ->multiple()  // Abilita la selezione multipla
                    ->options(
                        // Recupera i valori unici della colonna 'type' dal database
                        fn() => PracticeOam::query()
                            ->pluck('name', 'name')  // 'valore' => 'etichetta'
                            ->sort()
                            ->toArray()
                    )
                    ->searchable(),  // Opzionale: aggiunge una barra di
                SelectFilter::make('mese')
                    ->label('Mese perfezionamento')
                    ->multiple()
                    ->options([
                        '01' => 'Gennaio',
                        '02' => 'Febbraio',
                        '03' => 'Marzo',
                        '04' => 'Aprile',
                        '05' => 'Maggio',
                        '06' => 'Giugno',
                        '07' => 'Luglio',
                        '08' => 'Agosto',
                        '09' => 'Settembre',
                        '10' => 'Ottobre',
                        '11' => 'Novembre',
                        '12' => 'Dicembre',
                    ])
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
