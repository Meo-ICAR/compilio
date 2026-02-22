<?php

namespace App\Filament\Resources\Addresses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('street'),
                TextInput::make('city'),
                TextInput::make('zip_code'),
                TextInput::make('address_type_id')
                    ->numeric(),
            ]);
    }
}
