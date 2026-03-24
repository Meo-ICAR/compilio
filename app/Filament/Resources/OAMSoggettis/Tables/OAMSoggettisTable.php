<?php

namespace App\Filament\Resources\OAMSoggettis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OAMSoggettisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('denominazione_sociale')
                    ->label('Denominazione Sociale')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        return $column->getState();
                    })
                    ->weight('bold'),
                TextColumn::make('persona')
                    ->label('Tipo Persona')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Fisica' => 'success',
                        'Giuridica' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('codice_fiscale')
                    ->label('Codice Fiscale')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Codice fiscale copiato!')
                    ->copyMessageDuration(1500)
                    ->placeholder('N/D'),
                TextColumn::make('numero_iscrizione')
                    ->label('Numero Iscrizione')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Numero iscrizione copiato!')
                    ->copyMessageDuration(1500),
                TextColumn::make('data_iscrizione')
                    ->label('Data Iscrizione')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('N/D'),
                TextColumn::make('stato')
                    ->label('Stato')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Attivo' => 'success',
                        'Sospeso' => 'warning',
                        'Cancellato' => 'danger',
                        'Revocato' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('data_stato')
                    ->label('Data Stato')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('N/D'),
                BooleanColumn::make('autorizzato_ad_operare')
                    ->label('Autorizzato')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('dipendente_collaboratore_di')
                    ->label('Collaboratore di')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('N/A'),
                TextColumn::make('numero_collaborazioni_attive')
                    ->label('Collaborazioni Attive')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state > 0 => 'primary',
                        $state === 0 => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('domicilio_sede_legale')
                    ->label('Sede Legale')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('N/D')
                    ->tooltip(function (TextColumn $column): ?string {
                        return $column->getState();
                    }),
            ])
            ->defaultSort('denominazione_sociale')
            ->filters([
                SelectFilter::make('persona')
                    ->label('Tipo Persona')
                    ->options([
                        'Fisica' => 'Persona Fisica',
                        'Giuridica' => 'Persona Giuridica',
                    ])
                    ->placeholder('Tutti'),
                SelectFilter::make('stato')
                    ->label('Stato')
                    ->options([
                        'Attivo' => 'Attivo',
                        'Sospeso' => 'Sospeso',
                        'Cancellato' => 'Cancellato',
                        'Revocato' => 'Revocato',
                    ])
                    ->placeholder('Tutti'),
                Filter::make('numero_iscrizione')
                    ->label('Numero iscrizione'),
                Filter::make('autorizzato_ad_operare')
                    ->label('Autorizzato ad Operare')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('autorizzato_ad_operare', true)),
                Filter::make('ha_collaborazioni')
                    ->label('Ha Collaborazioni Attive')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('numero_collaborazioni_attive', '>', 0)),
                Filter::make('data_iscrizione_range')
                    ->label('Periodo Iscrizione')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dal')
                            ->placeholder('gg/mm/aaaa'),
                        DatePicker::make('until')
                            ->label('Al')
                            ->placeholder('gg/mm/aaaa'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_iscrizione', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('data_iscrizione', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Visualizza')
                    ->modalHeading('Dettagli Soggetto OAM')
                    ->modalWidth('2xl'),
                EditAction::make()
                    ->label('Modifica'),
            ]);
    }
}
