<?php

namespace App\Filament\Resources\Agents\Tables;

use App\Filament\Imports\AgentsImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class AgentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Agente')
                    ->searchable(),
                TextColumn::make('coordinatedBy.name')
                    ->label('Coordinato da (Dip.)')
                    ->searchable()
                    ->placeholder('Nessuno'),
                TextColumn::make('coordinatedByAgent.name')
                    ->label('Coordinato da (Agente)')
                    ->searchable()
                    ->placeholder('Nessuno'),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable(),
                TextColumn::make('oam')
                    ->label('Numero OAM')
                    ->searchable(),
                TextColumn::make('oam_at')
                    ->label('Data OAM')
                    ->date()
                    ->sortable(),
                TextColumn::make('oam_name')
                    ->label('Nome OAM')
                    ->searchable(),
                TextColumn::make('stipulated_at')
                    ->label('Stipula')
                    ->date()
                    ->sortable(),
                TextColumn::make('dismissed_at')
                    ->label('Cessazione')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable(),
                TextColumn::make('contribute')
                    ->label('Contributo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contributeFrequency')
                    ->label('Frequenza')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contributeFrom')
                    ->label('Valido dal')
                    ->date()
                    ->sortable(),
                TextColumn::make('remburse')
                    ->label('Rimborso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->label('P.IVA')
                    ->searchable(),
                TextColumn::make('vat_name')
                    ->label('Ragione Sociale')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Aggiornato')
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
            ]);
    }
}
