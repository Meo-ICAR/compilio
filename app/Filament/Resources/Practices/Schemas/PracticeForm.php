<?php

namespace App\Filament\Resources\Practices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_mandate_id')
                    ->label('Mandato Cliente')
                    ->relationship('clientMandate', 'id')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Mandato del cliente associato alla pratica'),
                Select::make('principal_id')
                    ->label('Mandante')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('agent_id')
                    ->label('Agente')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('employee_id')
                    ->label('Dipendente che segue la pratica')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Dipendente interno responsabile della pratica'),
                Select::make('practice_status_id')
                    ->label('Stato Pratica')
                    ->relationship('practiceStatus', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Seleziona lo stato attuale della pratica'),
                TextInput::make('stato_pratica')
                    ->label('Stato Pratica Originale')
                    ->maxLength(255)
                    ->nullable()
                    ->helperText('Stato pratica originale da sistema esterno'),
                TextInput::make('name')
                    ->label('Nome Pratica')
                    ->required(),
                TextInput::make('CRM_code')
                    ->label('Codice CRM')
                    ->required(),
                TextInput::make('principal_code')
                    ->label('Codice Mandante')
                    ->required(),
                TextInput::make('tipo_prodotto')
                    ->label('Tipo Prodotto')
                    ->maxLength(255)
                    ->nullable()
                    ->helperText('Tipo prodotto CRM'),
                TextInput::make('amount')
                    ->label('Importo')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                TextInput::make('net')
                    ->label('Netto')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                Select::make('practice_scope_id')
                    ->label('Ambito')
                    ->relationship('practiceScope', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'istruttoria' => 'Istruttoria',
                        'deliberata' => 'Deliberata',
                        'erogata' => 'Erogata',
                        'respinta' => 'Respinta',
                        'annullata' => 'Annullata',
                    ])
                    ->default('istruttoria')
                    ->required(),
                Select::make('statoproforma')
                    ->label('Stato Proforma')
                    ->options([
                        'Inserito' => 'Inserito',
                        'Sospeso' => 'Sospeso',
                        'Annullato' => 'Annullato',
                        'Inviato' => 'Inviato',
                        'Abbinato' => 'Abbinato',
                    ])
                    ->nullable()
                    ->helperText('Stato proforma: Inserito / Sospeso / Annullato / Inviato / Abbinato'),
                DatePicker::make('inserted_at')
                    ->label('Data Inserimento')
                    ->nullable()
                    ->helperText('Data inserimento pratica'),
                DatePicker::make('erogated_at')
                    ->label('Data Erogazione')
                    ->nullable()
                    ->helperText('Data erogazione finanziamento / stipula mutuo notaio'),
                DatePicker::make('rejected_at')
                    ->label('Data Rifiuto')
                    ->nullable()
                    ->helperText('Data rifiuto pratica'),
                DatePicker::make('status_at')
                    ->label('Data Stato')
                    ->nullable()
                    ->helperText('Data stato perfezionata ovvero possibile emissione proforma ad agente'),
                DatePicker::make('perfected_at')
                    ->label('Data Perfezionamento')
                    ->required(),
                TextInput::make('brokerage_fee')
                    ->label('Provvigione')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->helperText('Provvigione pattuita per questa pratica'),
                Textarea::make('description')
                    ->label('Descrizione Pratica')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('rejected_reason')
                    ->label('Causale Rifiuto')
                    ->rows(2)
                    ->nullable()
                    ->helperText('Causale rifiuto pratica es. Rifiutata banca'),
                Textarea::make('annotation')
                    ->label('Annotazioni Interne')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Annotazioni interne sulla pratica'),
                Toggle::make('is_active')
                    ->label('Attiva')
                    ->default(true),
            ]);
    }
}
