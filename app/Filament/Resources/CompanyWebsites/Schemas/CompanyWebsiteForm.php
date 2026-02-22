<?php

namespace App\Filament\Resources\CompanyWebsites\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyWebsiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('domain')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('principal_id')
                    ->numeric(),
                Toggle::make('is_active'),
            ]);
    }
}
