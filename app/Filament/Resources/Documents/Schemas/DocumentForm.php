<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('practice_id')
                    ->relationship('practice', 'name')
                    ->required(),
                Select::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('file_path'),
                TextInput::make('status')
                    ->required()
                    ->default('uploaded'),
                DatePicker::make('expires_at'),
            ]);
    }
}
