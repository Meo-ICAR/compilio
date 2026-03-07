<?php

namespace App\Filament\Resources\PracticeOams\Tables;

use App\Exports\PracticeOamBaseExport;
use App\Filament\Exports\PracticeOamAnaliticoExporter;
use App\Filament\Exports\PracticeOamExporter;
use App\Models\PracticeOam;
use App\Models\PracticeOamBase;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;  // USA LA FACADE!

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
            ->headerActions([
                Action::make('exportSintetico')
                    ->label('Export Prospetto BASE')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        // Get current company from authenticated user or session
                        $currentCompanyId = auth()->user()?->company_id ?? session('current_company_id');

                        // 1. Svuota la tabella di appoggio
                        PracticeOamBase::truncate();

                        // 2. Query raggruppata con gli alias che hai definiti
                        $query = DB::table('practice_oams')
                            ->select([
                                'oam_name as B_OAM',
                                DB::raw('SUM(is_conventioned) as C_Convenzionata'),
                                DB::raw('SUM(is_notconventioned) as D_Non_Convenzionata'),
                                DB::raw('SUM(is_perfected) as E_Intermediate'),
                                DB::raw('SUM(is_working) as F_Lavorazione'),
                                DB::raw('SUM(erogato) as G_Erogato'),
                                DB::raw('SUM(erogato_lavorazione) as H_Erogato_Lavorazione'),
                                DB::raw('SUM(compenso_cliente) as I_Provvigione_Cliente'),
                                DB::raw('SUM(compenso) as J_Provvigione_Istituto'),
                                DB::raw('SUM(compenso_lavorazione) as K_Provvigione_Istituto_Lavorazione'),
                                DB::raw('SUM(provvigione) as O_Provvigione_Rete'),
                            ])
                            ->groupBy('oam_name');

                        // Filter by company if company_id is available
                        if ($currentCompanyId) {
                            $query->where('company_id', $currentCompanyId);
                        }

                        $totals = $query->get();

                        // 3. Inserimento massivo (molto veloce)
                        foreach ($totals as $row) {
                            PracticeOamBase::create([
                                'company_id' => $currentCompanyId,
                                'B_OAM' => $row->B_OAM,
                                'C_Convenzionata' => $row->C_Convenzionata,
                                'D_Non_Convenzionata' => $row->D_Non_Convenzionata,
                                'E_Intermediate' => $row->E_Intermediate,
                                'F_Lavorazione' => $row->F_Lavorazione,
                                'G_Erogato' => $row->G_Erogato,
                                'H_Erogato_Lavorazione' => $row->H_Erogato_Lavorazione,
                                'I_Provvigione_Cliente' => $row->I_Provvigione_Cliente,
                                'J_Provvigione_Istituto' => $row->J_Provvigione_Istituto,
                                'K_Provvigione_Istituto_Lavorazione' => $row->K_Provvigione_Istituto_Lavorazione,
                                'O_Provvigione_Rete' => $row->O_Provvigione_Rete,
                            ]);
                        }
                        // 4. Download immediato dalla tabella piatta
                        return Excel::download(
                            new class implements FromQuery, WithHeadings, WithMapping {
                                public function query()
                                {
                                    $currentCompanyId = auth()->user()?->company_id ?? session('current_company_id');
                                    $query = PracticeOamBase::query()->select([
                                        'B_OAM', 'C_Convenzionata', 'D_Non_Convenzionata', 'E_Intermediate',
                                        'F_Lavorazione', 'G_Erogato', 'H_Erogato_Lavorazione',
                                        'I_Provvigione_Cliente', 'J_Provvigione_Istituto',
                                        'K_Provvigione_Istituto_Lavorazione',
                                        'O_Provvigione_Rete'
                                    ]);

                                    // Filter by company if company_id is available
                                    if ($currentCompanyId) {
                                        $query->where('company_id', $currentCompanyId);
                                    }

                                    return $query;
                                }

                                public function map($row): array
                                {
                                    return [
                                        0,
                                        $row->B_OAM,
                                        (int) $row->C_Convenzionata,  // Cast esplicito a intero
                                        (int) $row->D_Non_Convenzionata,
                                        (int) $row->E_Intermediate,
                                        (int) $row->F_Lavorazione,
                                        (float) $row->G_Erogato,  // Cast a float per i monetari
                                        (float) $row->H_Erogato_Lavorazione,
                                        (float) $row->I_Provvigione_Cliente,
                                        (float) $row->J_Provvigione_Istituto,
                                        (float) $row->K_Provvigione_Istituto_Lavorazione,
                                        0,
                                        0,
                                        0,
                                        (float) $row->O_Provvigione_Rete,
                                    ];
                                }

                                public function headings(): array
                                {
                                    return [
                                        '-',  // Colonna A vuota '',  // Colonna A vuota
                                        'B-OAM', 'C-Convenzionata', 'D-Non_Convenzionata', 'E-Intermediate',
                                        'F-Lavorazione', 'G-Erogato', 'H-Lavorazione',
                                        'I-Provv_Cliente', 'J-Provv_Istituto',
                                        'K-Provv_Istituto_Lavorazione',
                                        '-',  //  L
                                        '-',  //  M
                                        '-',  //  N
                                        'O-Provv_Rete'
                                    ];
                                }
                            },
                            'OAM_Base_' . now()->format('d-m-Y') . '.xlsx'
                        );
                    }),
                ExportAction::make('Dettagliato')
                    ->label('Export elenco pratiche')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->exporter(PracticeOamExporter::class)
                    ->columnMapping(false)  // Impedisce all'utente di deselezionare colonne se vuoi un report fisso
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
