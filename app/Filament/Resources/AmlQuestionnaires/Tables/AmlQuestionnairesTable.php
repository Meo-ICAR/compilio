<?php

namespace App\Filament\Resources\AmlQuestionnaires\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AmlQuestionnairesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.last_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('practice.practice_number')
                    ->label('Pratica')
                    ->searchable()
                    ->placeholder('Generico'),
                TextColumn::make('agent.name')
                    ->label('Agente'),
                BadgeColumn::make('risk_level')
                    ->label('Rischio')
                    ->colors([
                        'success' => 'basso',
                        'warning' => 'medio',
                        'danger' => 'alto',
                    ]),
                TextColumn::make('valid_until')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($state) => $state->isPast() ? 'danger' : 'success')
                    ->weight(fn($state) => $state->isPast() ? 'bold' : 'normal'),
            ])
            ->filters([
                SelectFilter::make('risk_level')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ]),
                // Filtro per vedere solo quelli scaduti
                Filter::make('scaduti')
                    ->query(fn($query) => $query->where('valid_until', '<', now()))
                    ->label('Mostra Scaduti')
                    ->toggle(),
            ])
            ->actions([
                EditAction::make(),
                // Azione personalizzata per scaricare il PDF direttamente dalla tabella
                Action::make('scarica_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn(AmlQuestionnaire $record) => $record->getFirstMediaUrl('documento_firmato'))
                    ->openUrlInNewTab()
                    ->visible(fn(AmlQuestionnaire $record) => $record->hasMedia('documento_firmato')),
            ]);
    }
}
