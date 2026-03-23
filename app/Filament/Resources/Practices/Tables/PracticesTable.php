<?php

namespace App\Filament\Resources\Practices\Tables;

use App\Filament\Imports\PracticesImporter;
use App\Filament\Traits\CanExportTable;
use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\Agent;
use App\Models\Practice;
use App\Models\PracticeStatus;
use App\Models\Principal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class PracticesTable
{
    use CanExportTable;
    use HasChecklistAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['principal', 'agent', 'practiceScope', 'practiceStatus', 'clientMandate', 'parentPractice']))
            ->paginated(['all', 10, 25, 50, 100])
            ->defaultSort('inserted_at', 'desc')
            ->selectable()
            ->reorderableColumns()
            ->columns([
                TextColumn::make('tipo_prodotto')
                    ->label('Tipo Prodotto')
                    ->searchable()
                    ->sortable()
                    ->summarize(Count::make()->label(''))
                    ->placeholder('Assente'),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun mandante'),
                TextColumn::make('inserted_at')
                    ->label('Data Inserimento')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('sended_at')
                    ->label('Istruttoria')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('approved_at')
                    ->label('Delibera')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('erogated_at')
                    ->label('Erogazione')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('perfected_at')
                    ->label('Perfezionata')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('invoice_at')
                    ->label('Fatturazione')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                IconColumn::make('is_invoiced')
                    ->label('Fatturata')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('client.full_name')
                    ->label('Contraente')
                    ->sortable()
                    ->placeholder('No cliente'),
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun agente'),
                TextColumn::make('stato_pratica')
                    ->label('Stato CRM')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuno stato'),
                TextColumn::make('name')
                    ->label('Nome Pratica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CRM_code')
                    ->label('Codice CRM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Montante')
                    ->money('EUR')
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('net')
                    ->label('Erogato')
                    ->money('EUR')
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('rejected_at')
                    ->label('Data Rifiuto')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('status_at')
                    ->label('Data Stato')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('statoproforma')
                    ->label('Stato Proforma')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Inserito' => 'blue',
                        'Sospeso' => 'yellow',
                        'Annullato' => 'red',
                        'Inviato' => 'green',
                        'Abbinato' => 'purple',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuno stato proforma'),
                TextColumn::make('brokerage_fee')
                    ->label('Provvigione')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->placeholder('Non definita'),
                TextColumn::make('rejected_reason')
                    ->label('Causale Rifiuto')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuna causale'),
                IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean(),
                IconColumn::make('isPerfected')
                    ->label('Perfezionata')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn($record) => $record->isPerfected()
                        ? ($record->perfected_at ? 'Perfezionata il: ' . $record->perfected_at->format('d/m/Y') : 'Perfezionata')
                        : 'Non perfezionata'),
                IconColumn::make('isWorking')
                    ->label('In Lavorazione')
                    ->boolean()
                    ->trueIcon('heroicon-s-clock')
                    ->falseIcon('heroicon-o-pause-circle')
                    ->color(fn($state) => $state ? 'warning' : 'gray')
                    ->tooltip(fn($record) => $record->isWorking() ? 'In lavorazione' : 'Non in lavorazione'),
                IconColumn::make('isRejected')
                    ->label('Respinta')
                    ->boolean()
                    ->trueIcon('heroicon-s-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->color(fn($state) => $state ? 'danger' : 'success')
                    ->tooltip(fn($record) => $record->isRejected() ? 'Respinta' : 'Non respinta'),
                TextColumn::make('client.name')
                    ->label('Cognome')
                    ->sortable()
                    ->placeholder('No cliente'),
                TextColumn::make('parentPractice.name')
                    ->label('Pratica Collegata')
                    ->sortable()
                    ->placeholder('Nessuna pratica collegata')
                    ->description(fn($record): string => $record->parentPractice?->CRM_code ?? '')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('principal_id')
                    ->label('Mandante')
                    ->multiple()
                    ->options(function () {
                        return Principal::all()
                            ->pluck('name', 'id')
                            ->sort();
                    })
                    ->searchable(),
                SelectFilter::make('agent_id')
                    ->label('Agente')
                    ->multiple()
                    ->options(function () {
                        return Agent::all()
                            ->pluck('name', 'id')
                            ->sort();
                    })
                    ->searchable(),
                SelectFilter::make('tipo_prodotto')
                    ->label('Filtra per Tipo')
                    ->multiple()  // Abilita la selezione multipla
                    ->options(
                        // Recupera i valori unici della colonna 'type' dal database
                        fn() => Practice::query()
                            ->whereNotNull('tipo_prodotto')  // Esclude null values
                            ->pluck('tipo_prodotto', 'tipo_prodotto')  // 'valore' => 'etichetta'
                            ->sort()
                            ->toArray()
                    )
                    ->searchable(),  // Opzionale: aggiunge una barra di ricerca nel dropdown
                SelectFilter::make('stato_pratica')
                    ->options(PracticeStatus::pluck('name', 'id'))
                    ->multiple()
                    ->label('Stato Pratica')
                    ->default(['PERFEZIONATA', 'IN AMMORTAMENTO']),
                TernaryFilter::make('isRejected')
                    ->label('Respinta')
                    ->placeholder('Tutte')
                    ->trueLabel('Respinte')
                    ->falseLabel('Non respinte')
                    ->default(null)
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('rejected_at'),
                        false: fn(Builder $query) => $query->whereNull('rejected_at'),
                        blank: fn(Builder $query) => $query,  // Show all
                    ),
                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('sended_at')
                            ->label('Inviate in Istruttoria'),
                        DateConstraint::make('approved_at')
                            ->nullable()
                            ->label('Pratiche deliberate'),
                        DateConstraint::make('erogated_at')
                            ->nullable()
                            ->label('Pratiche erogate'),
                        DateConstraint::make('perfected_at')
                            ->nullable()
                            ->label('Pratiche perfezionate'),
                        DateConstraint::make('invoice_at')
                            ->nullable()
                            ->label('Fatturate'),
                        DateConstraint::make('rejected_at')
                            ->nullable()
                            ->label('Pratiche Rifiutate'),
                        NumberConstraint::make('amount')
                            ->nullable()
                            ->label('Montante'),
                        NumberConstraint::make('net')
                            ->nullable()
                            ->label('Erogato'),
                        NumberConstraint::make('brokerage_fee')
                            ->nullable()
                            ->label('Provvigioni'),
                        TextConstraint::make('stato_pratica')
                            ->label('Stato Pratica'),
                    ])
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('checklist')
                    ->label(fn($record) => $record->tipo_prodotto ?: 'Checklist')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->action(function ($record) {
                        // Get the checklist actions for this record's tipo_prodotto
                        $actions = self::getChecklistActions(
                            code: $record->tipo_prodotto,
                            label: $record->tipo_prodotto ?: 'Checklist'
                        );

                        // Check if checklist exists for this record
                        $exists = DB::table('checklists')
                            ->where('target_id', $record->id)
                            ->where('target_type', get_class($record))
                            ->where('code', $record->tipo_prodotto)
                            ->exists();

                        if ($exists) {
                            // Execute manage action (second action in array)
                            $manageAction = $actions[1];
                            $manageAction->call(['record' => $record]);
                        } else {
                            // Execute generate action (first action in array)
                            $generateAction = $actions[0];
                            $generateAction->call(['record' => $record]);
                        }
                    }),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    static::getExportBulkAction(),  // 2. Richiama l'azione dal trait
                    //   DeleteBulkAction::make(),
                ]),
            ]);
    }
}
