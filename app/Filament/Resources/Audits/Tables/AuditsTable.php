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
            ->modifyQueryUsing(fn($query) => $query->with(['principal', 'agent', 'regulatoryBody', 'client']))
            ->columns([
                TextColumn::make('requester_type')
                    ->label('Tipo Richiedente')
                    ->badge(),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('regulatoryBody.name')
                    ->label('Ente Regolatore')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable(),
                TextColumn::make('emails')
                    ->label('Email Notifiche')
                    ->searchable(),
                TextColumn::make('reference_period')
                    ->label('Periodo Riferimento')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Data Inizio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Data Fine')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge(),
                TextColumn::make('overall_score')
                    ->label('Valutazione')
                    ->searchable(),
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
