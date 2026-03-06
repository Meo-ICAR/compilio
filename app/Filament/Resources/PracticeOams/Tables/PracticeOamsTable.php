<?php

namespace App\Filament\Resources\PracticeOams\Tables;

use App\Models\PracticeOam;
use Filament\Actions\Action;
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
use Maatwebsite\Excel\Excel;

class PracticeOamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(['all', 10, 25, 50, 100])
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
                    ->label('B-OAM Code')
                    ->sortable(),
                TextColumn::make('tipo_prodotto')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_conventioned')
                    ->label('C - Convenzionata')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                IconColumn::make('is_notconventioned')
                    ->label('D - NON Convenz.')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                IconColumn::make('is_perfected')
                    ->label('E - Intermediate')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                IconColumn::make('is_working')
                    ->label('F - Lavorazione')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                TextColumn::make('erogato')
                    ->label('G - Erogato')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('erogato_lavorazione')
                    ->label('H - Lavorazione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_cliente')
                    ->label('I - Provv. Cliente')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso')
                    ->label('J - Provv. Istituto')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_premio')
                    ->label('K - Premio')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_assicurazione')
                    ->label('L - Assicurativi')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione')
                    ->label('O - Provv. Rete')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_assicurazione')
                    ->label('P - Assic. Rete')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                IconColumn::make('is_cancel')
                    ->label('S - N.Rivalse')
                    ->boolean()
                    ->summarize(
                        Sum::make()
                            ->label(false)
                            // Questo forza il database a trattare true come 1 e false come 0
                            ->numeric()
                    )
                    ->sortable(),
                TextColumn::make('storno')
                    ->label('T - Rivalsa')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('compenso_rimborso')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_premio')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_storno')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_rimborso')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Mandante')
                    ->sortable(),
                TextColumn::make('practice.clients.name')
                    ->label('Cliente')
                    ->sortable(),
                TextColumn::make('practice.CRM_code')
                    ->label('Codice')
                    ->sortable(),
                TextColumn::make('practice.name')
                    ->label('Pratica')
                    ->sortable(),
                TextColumn::make('practice.inserted_at')
                    ->label('Inserita')
                    ->date()
                    ->sortable(),
                TextColumn::make('practice.erogated_at')
                    ->label('Erogata')
                    ->date()
                    ->sortable(),
                TextColumn::make('practice.principal.type')
                    ->label('Tipo fin.')
                    ->sortable(),
                TextColumn::make('compenso_lavorazione')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('provvigione_lavorazione')
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
                Filter::make('is_working')
                    ->label('Lavorazione')
                    ->query(fn($query) => $query->where('is_working', true)),
                Filter::make('is_perfected')
                    ->label('Erogata')
                    ->query(fn($query) => $query->where('is_perfected', true)),
                SelectFilter::make('oam_code')
                    ->label('Filtra per Codice OAM')
                    ->multiple()  // Abilita la selezione multipla
                    ->options(
                        // Recupera i valori unici della colonna 'type' dal database
                        fn() => PracticeOam::query()
                            ->pluck('oam_code', 'oam_code')  // 'valore' => 'etichetta'
                            ->sort()
                            ->toArray()
                    )
                    ->searchable(),  // Opzionale: aggiunge una barra di ricerca nel dropdown
                SelectFilter::make('tipo_prodotto')
                    ->label('Filtra per Tipo Prodotto')
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
                Action::make('export-excel')
                    ->label('Esporta Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        // Get current query with filters applied
                        $query = $livewire->getFilteredTableQuery();
                        $records = $query->get();

                        // Prepare data for export
                        $exportData = [];
                        foreach ($records as $record) {
                            $exportData[] = [
                                'Codice OAM' => $record->practice->scopeOAM->oam_code ?? '',
                                'Tipo OAM' => $record->practice->scopeOAM->tipo_prodotto ?? '',
                                'Prodotto' => $record->tipo_prodotto,
                                'Convenzionata' => $record->is_conventioned ? 'Sì' : 'No',
                                'Codice Pratica' => $record->practice->CRM_code ?? '',
                                'Nome Pratica' => $record->practice->name ?? '',
                                'Cliente' => $record->practice->clients->full_name ?? '',
                                'Provvigione Assicurazione' => number_format($record->provvigione_assicurazione, 2, ',', '.') . ' €',
                                'Provvigione Storno' => number_format($record->provvigione_storno, 2, ',', '.') . ' €',
                                'Importo Lordo' => number_format($record->importo_lordo, 2, ',', '.') . ' €',
                                'Netto Incassato' => number_format($record->netto_incassato, 2, ',', '.') . ' €',
                                'Erogato' => number_format($record->erogato, 2, ',', '.') . ' €',
                                'Data Perfezionamento' => $record->data_perfezionamento ? $record->data_perfezionamento->format('d/m/Y') : '',
                                'Mandante' => $record->name,
                                'Mese' => $record->mese,
                            ];
                        }

                        // Generate filename with current date
                        $filename = 'practice_oams_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

                        // Export using Maatwebsite Excel
                        return Excel::download(
                            new \App\Exports\PracticeOamsExport($exportData),
                            $filename
                        );
                    }),
            ]);
    }
}
