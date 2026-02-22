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
            ]);
    }
}
