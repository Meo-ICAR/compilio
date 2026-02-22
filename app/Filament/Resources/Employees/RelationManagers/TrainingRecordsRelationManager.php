<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Models\TrainingRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingRecords';

    protected static ?string $title = 'Formazione Frequentata';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('trainingSession'))
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('trainingSession.name')
                    ->label('Sessione Formativa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('completion_date')
                    ->label('Data Completamento')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Note')
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuova Registrazione'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifica'),
                DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status')
                    ->label('Stato')
                    ->default('in_progress')
                    ->required(),
                TextInput::make('completion_date')
                    ->label('Data Completamento')
                    ->date()
                    ->required(),
                TextInput::make('notes')
                    ->label('Note')
                    ->rows(3),
            ]);
    }
}
