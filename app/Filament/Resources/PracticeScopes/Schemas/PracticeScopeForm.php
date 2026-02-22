<?php

namespace App\Filament\Resources\PracticeScopes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PracticeScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('oam_code')
                    ->required(),
            ]);
    }
}
