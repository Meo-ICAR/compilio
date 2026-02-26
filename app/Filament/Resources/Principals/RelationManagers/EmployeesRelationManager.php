<?php

namespace App\Filament\Resources\Principals\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $title = 'Dipendenti Autorizzati';

    protected static ?string $modelLabel = 'Dipendente';

    protected static ?string $pluralModelLabel = 'Dipendenti';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('usercode')
                    ->required()
                    ->unique()
                    ->label('Codice Utente')
                    ->helperText('Codice identificativo univoco del dipendente'),
                Forms\Components\TextInput::make('description')
                    ->label('Descrizione')
                    ->helperText('Ruolo o note sul dipendente')
                    ->nullable(),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->label('Data Inizio')
                    ->helperText('Data di inizio autorizzazione'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Data Fine')
                    ->helperText('Data di fine autorizzazione (lasciare vuoto per indeterminato)')
                    ->nullable(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true)
                    ->helperText('Stato attuale del dipendente'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('usercode')
            ->columns([
                Tables\Columns\TextColumn::make('usercode')
                    ->label('Codice Utente')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Codice copiato!'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inizio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fine')
                    ->date()
                    ->sortable()
                    ->placeholder('Indeterminato'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_currently_active')
                    ->label('Stato Corrente')
                    ->getStateUsing(fn($record) => $record->is_currently_active ? 'Attivo' : 'Non Attivo')
                    ->badge()
                    ->color(fn($record) => $record->is_currently_active ? 'success' : 'danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Stato')
                    ->options([
                        '1' => 'Attivo',
                        '0' => 'Inattivo',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
