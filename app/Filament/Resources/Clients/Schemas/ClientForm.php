<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_person')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('tax_code'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                Toggle::make('is_pep'),
                Toggle::make('is_sanctioned'),
                Select::make('client_type_id')
                    ->label('Tipo Cliente')
                    ->options(\App\Models\ClientType::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }
}
