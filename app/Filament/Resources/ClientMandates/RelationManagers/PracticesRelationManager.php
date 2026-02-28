<?php

namespace App\Filament\Resources\ClientMandates\RelationManagers;

use App\Filament\Resources\Practices\PracticeResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PracticesRelationManager extends RelationManager
{
    protected static string $relationship = 'practices';

    protected static ?string $modelLabel = 'Pratiche';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome Pratica')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('CRM_code')
                    ->label('Codice CRM')
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label('Importo')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Pratica')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('CRM_code')
                    ->label('Codice CRM')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'working' => 'warning',
                        'perfected' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'working' => 'In Lavorazione',
                        'perfected' => 'Perfezionata',
                        'rejected' => 'Respinta',
                    ]),
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
