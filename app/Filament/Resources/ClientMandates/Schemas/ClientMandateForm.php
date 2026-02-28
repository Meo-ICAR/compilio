<?php

namespace App\Filament\Resources\ClientMandates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientMandateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('numero_mandato')
                    ->required(),
                DatePicker::make('data_firma_mandato')
                    ->required(),
                DatePicker::make('data_scadenza_mandato')
                    ->required(),
                TextInput::make('importo_richiesto_mandato')
                    ->numeric(),
                TextInput::make('scopo_finanziamento'),
                DatePicker::make('data_consegna_trasparenza'),
                Select::make('stato')
                    ->options([
            'attivo' => 'Attivo',
            'concluso_con_successo' => 'Concluso con successo',
            'scaduto' => 'Scaduto',
            'revocato' => 'Revocato',
        ])
                    ->default('attivo')
                    ->required(),
            ]);
    }
}
