<?php

namespace App\Filament\Resources\Mandates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MandateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('principal_id')
                    ->required()
                    ->numeric(),
                TextInput::make('mandate_number')
                    ->required(),
                TextInput::make('name'),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Toggle::make('is_exclusive'),
                Select::make('status')
                    ->options([
            'ATTIVO' => 'A t t i v o',
            'SCADUTO' => 'S c a d u t o',
            'RECEDUTO' => 'R e c e d u t o',
            'SOPESO' => 'S o p e s o',
        ])
                    ->default('ATTIVO'),
                TextInput::make('contract_file_path'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
