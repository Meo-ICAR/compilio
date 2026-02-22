<?php

namespace App\Filament\Resources\PrincipalScopes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrincipalScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('principal_id')
                    ->required()
                    ->numeric(),
                TextInput::make('practice_scope_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
