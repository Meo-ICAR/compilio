<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['auditable', 'principal', 'agent', 'regulatoryBody', 'client']))
            ->columns([
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Tipo Oggetto')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Company' => 'Azienda',
                        'App\Models\Agent' => 'Agente',
                        'App\Models\Employee' => 'Dipendente',
                        'App\Models\Client' => 'Cliente',
                        'App\Models\Principal' => 'Mandante',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                TextColumn::make('auditable.name')
                    ->label('Oggetto Audit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester_type')
                    ->label('Tipo Richiedente')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'OAM' => 'OAM',
                        'PRINCIPAL' => 'Mandante',
                        'INTERNAL' => 'Interno',
                        'EXTERNAL' => 'Esterno',
                        default => $state,
                    })
                    ->badge(),
                TextColumn::make('principal.name')
                    ->label('Mandante (Legacy)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('agent.name')
                    ->label('Agente (Legacy)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('regulatoryBody.name')
                    ->label('Ente Regolatore')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('emails')
                    ->label('Email Notifiche')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference_period')
                    ->label('Periodo Riferimento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->label('Data Inizio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Data Fine')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'PROGRAMMATO' => 'Programmato',
                        'IN_CORSO' => 'In Corso',
                        'COMPLETATO' => 'Completato',
                        'ARCHIVIATO' => 'Archiviato',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PROGRAMMATO' => 'info',
                        'IN_CORSO' => 'warning',
                        'COMPLETATO' => 'success',
                        'ARCHIVIATO' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('overall_score')
                    ->label('Valutazione')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
