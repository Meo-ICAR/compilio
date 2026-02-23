<?php

namespace App\Filament\Resources\ClientTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Toggle::make('is_person')
                    ->label('Persona Fisica')
                    ->helperText('Indica se questo tipo di cliente è una persona fisica')
                    ->default(true),
                Toggle::make('is_company')
                    ->label('Società/Azienda')
                    ->helperText('Indica se questo tipo di cliente è una società o azienda')
                    ->default(false),
            ]);
    }
}
