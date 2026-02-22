<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Models\SoftwareApplication;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class SoftwareApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'softwareApplications';

    protected static ?string $title = 'Software Applicativi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Software')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider_name')
                    ->label('Produttore')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ATTIVO' => 'success',
                        'SOSPESO' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\AttachAction::make()
                    ->label('Associa Software')
                    ->form(fn (\Filament\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'ATTIVO' => 'Attivo',
                                'SOSPESO' => 'Sospeso',
                            ])
                            ->default('ATTIVO')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Note Aziendali'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modifica Associazione')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'ATTIVO' => 'Attivo',
                                'SOSPESO' => 'Sospeso',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Note Aziendali'),
                    ]),
                \Filament\Actions\DetachAction::make()
                    ->label('Rimuovi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    \Filament\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
