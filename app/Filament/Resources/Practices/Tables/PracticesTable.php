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
            ->modifyQueryUsing(fn($query) => $query->with(['principal', 'agent', 'practiceScope']))
            ->columns([
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
                IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean(),
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
