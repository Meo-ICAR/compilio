<?php

namespace App\Filament\Resources\Principals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrincipalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('abi'),
                DatePicker::make('stipulated_at'),
                DatePicker::make('dismissed_at'),
                TextInput::make('vat_number'),
                TextInput::make('vat_name'),
                TextInput::make('type'),
                TextInput::make('oam'),
                TextInput::make('ivass'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('company_id')
                    ->required(),
                TextInput::make('mandate_number')
                    ->required(),
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
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
