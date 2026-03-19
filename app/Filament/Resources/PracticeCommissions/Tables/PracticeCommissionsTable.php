<?php

namespace App\Filament\Resources\PracticeCommissions\Tables;

use App\Filament\Imports\PracticeCommissionsImporter;
use App\Models\PracticeCommission;
use App\Traits\CanExportTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class PracticeCommissionsTable
{
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn() => PracticeCommission::query()->where('is_payment', true))
            ->paginated(['all', 10, 25, 50, 100])
            ->defaultSort('perfected_at', 'desc')
            ->columns([
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->sortable(),
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
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(PracticeCommissionsImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
