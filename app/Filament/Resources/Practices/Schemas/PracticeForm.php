<?php

namespace App\Filament\Resources\Practices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextInput::make('name')
                    ->label('Nome Pratica')
                    ->required(),
                TextInput::make('CRM_code')
                    ->label('Codice CRM')
                    ->required(),
                TextInput::make('principal_code')
                    ->label('Codice Mandante')
                    ->required(),
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
                DatePicker::make('perfected_at')
                    ->label('Data Perfezionamento')
                    ->required(),
                TextInput::make('brokerage_fee')
                    ->label('Provvigione')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->helperText('Provvigione pattuita per questa pratica'),
                Toggle::make('is_active')
                    ->label('Attiva')
                    ->default(true),
            ]);
    }
}
