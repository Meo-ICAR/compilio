<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                DatePicker::make('expires_at')
                    ->label('Data scadenza'),
                DatePicker::make('emitted_at')
                    ->label('Data emissione'),
                TextInput::make('docnumber')
                    ->label('Numero documento'),
                TextInput::make('emitted_by')
                    ->label('Ente rilascio'),
                Toggle::make('is_signed')
                    ->label('Firmato')
                    ->default(false),
            ]);
    }
}
