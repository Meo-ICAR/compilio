<?php

namespace App\Filament\Resources\Practices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class PracticesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['principal', 'agent', 'practiceScope', 'practiceStatus', 'clientMandate']))
            ->columns([
                TextColumn::make('clientMandate.id')
                    ->label('ID Mandato')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('clients_names')
                    ->label('Contraenti')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun cliente'),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun mandante'),
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun agente'),
                TextColumn::make('practiceStatus.name')
                    ->label('Stato Pratica')
                    ->badge()
                    ->color(fn($record) => $record->practiceStatus?->color ?? 'gray')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessuno stato'),
                TextColumn::make('stato_pratica')
                    ->label('Stato Originale')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuno stato originale'),
                TextColumn::make('name')
                    ->label('Nome Pratica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CRM_code')
                    ->label('Codice CRM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal_code')
                    ->label('Codice Mandante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo_prodotto')
                    ->label('Tipo Prodotto')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessun tipo prodotto'),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('net')
                    ->label('Netto')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('practiceScope.name')
                    ->label('Ambito')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun ambito'),
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
                TextColumn::make('inserted_at')
                    ->label('Data Inserimento')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('erogated_at')
                    ->label('Data Erogazione')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
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
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn($state) => \App\Models\PracticeStatus::where('name', $state)->value('color') ?? 'gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->label('Data Perfezionamento')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('brokerage_fee')
                    ->label('Provvigione')
                    ->money('EUR')
                    ->sortable()
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
                TextColumn::make('updated_at')
                    ->label('Data Aggiornamento')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                \Filament\Actions\ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(\App\Filament\Imports\PracticesImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
