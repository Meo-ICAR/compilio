<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('street')
                    ->label('Via')
                    ->required()
                    ->maxLength(255),
                TextInput::make('city')
                    ->label('Città')
                    ->required()
                    ->maxLength(100),
                TextInput::make('zip_code')
                    ->label('CAP')
                    ->maxLength(5),
                Select::make('address.address_type_id')
                    ->label('Tipo Indirizzo')
                    ->options(\App\Models\AddressType::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }
}
