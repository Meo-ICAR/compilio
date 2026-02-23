<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';
    protected static ?string $title = 'Contraenti';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Contraenti')
            ->modifyQueryUsing(fn($query) => $query->with('clientType'))
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('pivot.role')
                    ->label('Ruolo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clientType.name')
                    ->label('Tipo Cliente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun tipo'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Permette di collegare un cliente esistente alla pratica
                // Permette di creare un nuovo cliente e collegarlo subito
                CreateAction::make(),
                AttachAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                // Permette di scollegare il cliente dalla pratica senza eliminarlo dal DB
                DetachAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
