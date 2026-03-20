<?php

namespace App\Filament\Resources\PracticeCommissions\Tables;

use App\Filament\Imports\PracticeCommissionsImporter;
use App\Filament\Traits\CanExportTable;
use App\Models\Agent;
use App\Models\PracticeCommission;
use App\Models\PracticeCommissionStatus;
use App\Models\Principal;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;

class PracticeCommissionsTable
{
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(['all', 10, 25, 50, 100])
            ->selectable()
            ->defaultSort('sended_at', 'desc')
            ->reorderableColumns()
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->columns([
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(
                        Sum::make()
                            ->money('EUR')
                            ->label('Totale Selezionati')
                            ->using(fn(array $selectedState): float => array_sum($selectedState))
                    )
                    ->sortable(),
                IconColumn::make('is_coordination')
                    ->label('Cord.')
                    ->sortable()
                    ->boolean(),
                TextColumn::make('perfected_at')
                    ->label('Perfezionata')
                    ->date()
                    ->sortable(),
                TextColumn::make('practice.name')
                    ->label('Pratica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('proforma.name')
                    ->label('Proforma')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('practiceCommissionStatus.name')
                    ->label('Stato Pagamento'),
                TextColumn::make('name')
                    ->label('Causale')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable(),
                IconColumn::make('is_coordination')
                    ->label('Coord.')
                    ->boolean(),
                TextColumn::make('cancellation_at')
                    ->label('Annullata')
                    ->date()
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->label('Num. fattura')
                    ->searchable(),
                TextColumn::make('invoice_at')
                    ->label('Fattura del')
                    ->date()
                    ->sortable(),
                TextColumn::make('paided_at')
                    ->label('Pagata il')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_payment')
                    ->label('Pagamento')
                    ->boolean(),
                IconColumn::make('is_storno')
                    ->label('Storno')
                    ->boolean(),
                IconColumn::make('is_enasarco')
                    ->label('Enasarco')
                    ->boolean(),
                TextColumn::make('CRM_code')
                    ->label('CRM')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_coordination')
                    ->label('Coordinamento'),
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
                TernaryFilter::make('is_payment')
                    ->label('Pagamento'),
                TernaryFilter::make('is_enasarco')
                    ->label('Enasarco'),
                TernaryFilter::make('is_insurance')
                    ->label('Assicurazione'),
                TernaryFilter::make('is_prize')
                    ->label('Premio da mandante')
                    ->query(fn($query) => $query->when(
                        request()->input('filters.is_prize') === '1',
                        fn($query) => $query->where('is_prize', true)
                    )->when(
                        request()->input('filters.is_prize') === '0',
                        fn($query) => $query->where('is_prize', false)
                    )),
                TernaryFilter::make('is_client')
                    ->label('Compenso da cliente'),
                TernaryFilter::make('is_coordination')
                    ->label('Coordinamento'),
                TernaryFilter::make('is_recurrent')
                    ->label('Compenso ricorrente'),
                SelectFilter::make('practice_commission_status_id')
                    ->label('Stato Pagamento')
                    ->options(function () {
                        return PracticeCommissionStatus::distinct()
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->multiple()
                    ->preload(),
                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('perfected_at')
                            ->label('Pratiche perfezionate'),
                        DateConstraint::make('invoice_at')
                            ->label('Fatturate'),
                    ])
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                // EditAction::make(),
                Action::make('toggleStatus')
                    ->label('')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function ($record) {
                        $record->update([
                            'proforma_stato' => $record->stato === 'Inserito' ? 'Sospeso' : 'Inserito'
                        ]);
                        Notification::make()
                            ->title('Stato aggiornato con successo')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record): bool => empty($record->proforma_id) && in_array($record->proforma_stato, ['Inserito', 'Sospeso']))
                    ->iconButton()
                    ->color('primary'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    static::getExportBulkAction(),  // 2. Richiama l'azione dal trait
                    //   DeleteBulkAction::make(),
                ]),
            ]);
    }
}
