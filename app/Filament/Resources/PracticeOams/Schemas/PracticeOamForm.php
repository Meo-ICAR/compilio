<?php

namespace App\Filament\Resources\PracticeOams\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeOamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('practice_id')
                    ->label('ID Pratica'),
                Select::make('oam_code_id')
                    ->label('Codice OAM')
                    ->relationship('oamCode', 'code')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('compenso')
                    ->label('Compenso')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('compenso_lavorazione')
                    ->label('Compenso Lavorazione')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('compenso_premio')
                    ->label('Compenso Premio')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('compenso_rimborso')
                    ->label('Compenso Rimborso')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('compenso_assicurazione')
                    ->label('Compenso Assicurazione')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('compenso_cliente')
                    ->label('Compenso Cliente')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('storno')
                    ->label('Storno')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('provvigione')
                    ->label('Provvigione')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('provvigione_lavorazione')
                    ->label('Provvigione Lavorazione')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('provvigione_premio')
                    ->label('Provvigione Premio')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('provvigione_rimborso')
                    ->label('Provvigione Rimborso')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('provvigione_assicurazione')
                    ->label('Provvigione Assicurazione')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('provvigione_storno')
                    ->label('Provvigione Storno')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                TextInput::make('erogato')
                    ->label('Erogato')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€')
                    ->nullable(),
                DatePicker::make('start_date')
                    ->label('Data Inizio')
                    ->nullable(),
                DatePicker::make('end_date')
                    ->label('Data Fine')
                    ->nullable(),
                DatePicker::make('perfected_at')
                    ->label('Data Perfezionamento')
                    ->nullable(),
                DatePicker::make('inserted_at')
                    ->label('Data Inserimento')
                    ->nullable(),
                DatePicker::make('invoice_at')
                    ->label('Data Fatturazione')
                    ->nullable(),
                DatePicker::make('accepted_at')
                    ->label('Data Accettazione')
                    ->nullable(),
                TextInput::make('name')
                    ->label('Nome Mandante')
                    ->nullable(),
                TextInput::make('tipo_prodotto')
                    ->label('Tipo Prodotto')
                    ->nullable(),
                TextInput::make('mese')
                    ->label('Mese')
                    ->numeric()
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true),
                Toggle::make('is_perfected')
                    ->label('Perfezionato')
                    ->default(false),
                Toggle::make('is_conventioned')
                    ->label('Convenzionato')
                    ->default(false),
                Toggle::make('is_notconventioned')
                    ->label('Non Convenzionato')
                    ->default(false),
                Toggle::make('is_invoice')
                    ->label('Fatturato')
                    ->default(false),
                Toggle::make('is_notconvenctioned')
                    ->label('Non Convenzionato (Altro)')
                    ->default(false),
                Toggle::make('is_working')
                    ->label('In Lavorazione')
                    ->default(true),
            ]);
    }
}
