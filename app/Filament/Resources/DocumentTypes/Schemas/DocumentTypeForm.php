<?php

namespace App\Filament\Resources\DocumentTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Toggle::make('is_person')
                    ->label('Per Persona Fisica')
                    ->helperText('Indica se questo tipo di documento è specifico per persone fisiche')
                    ->default(false),
                Toggle::make('is_signed')
                    ->label('Richiede Firma')
                    ->helperText('Indica se il documento deve essere firmato')
                    ->default(false),
                Toggle::make('is_stored')
                    ->label('Conservazione Sostitutiva')
                    ->helperText('Indica se il documento deve avere conservazione sostitutiva')
                    ->default(false),
                TextInput::make('duration')
                    ->label('Durata Validità (giorni)')
                    ->numeric()
                    ->helperText('Validità dal rilascio in giorni'),
                TextInput::make('emitted_by')
                    ->label('Ente Rilascio')
                    ->helperText('Ente che emette il documento'),
                Toggle::make('is_sensible')
                    ->label('Dati Sensibili')
                    ->helperText('Indica se contiene dati sensibili')
                    ->default(false),
            ]);
    }
}
