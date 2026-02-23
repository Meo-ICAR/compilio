<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use App\Models\PracticeCommission;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PracticeCommissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'practiceCommissions';

    protected static ?string $title = 'Commissioni Pratica';

    public function table(Tables\Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('practice_commissions.company_id', auth()->user()->company_id))
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->label('Percentuale')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Note')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Creazione')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Data Aggiornamento')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'PENDING' => 'In Attesa',
                        'APPROVED' => 'Approvata',
                        'REJECTED' => 'Rifiutata',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nuova Commissione'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifica'),
                Tables\Actions\DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('agent_id')
                    ->label('Agent')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->label('Importo')
                    ->numeric()
                    ->prefix('€')
                    ->required()
                    ->step(0.01),
                TextInput::make('percentage')
                    ->label('Percentuale')
                    ->numeric()
                    ->suffix('%')
                    ->max(100)
                    ->step(0.01),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'PENDING' => 'In Attesa',
                        'APPROVED' => 'Approvata',
                        'REJECTED' => 'Rifiutata',
                    ])
                    ->default('PENDING')
                    ->required(),
                Textarea::make('notes')
                    ->label('Note')
                    ->rows(3),
            ]);
    }
}
