<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome Agente')
                    ->required(),
                Select::make('coordinated_by_id')
                    ->label('Coordinato da (Dipendente)')
                    ->relationship('coordinatedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Seleziona un dipendente coordinatore'),
                Select::make('coordinated_by_agent_id')
                    ->label('Coordinato da (Agente)')
                    ->relationship('coordinatedByAgent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Seleziona un agente coordinatore'),
                TextInput::make('description')
                    ->label('Descrizione'),
                TextInput::make('oam')
                    ->label('Numero OAM'),
                DatePicker::make('oam_at')
                    ->label('Data Iscrizione OAM'),
                TextInput::make('oam_name')
                    ->label('Nome OAM'),
                DatePicker::make('stipulated_at')
                    ->label('Data Stipula'),
                DatePicker::make('dismissed_at')
                    ->label('Data Cessazione'),
                TextInput::make('type')
                    ->label('Tipo'),
                TextInput::make('contribute')
                    ->label('Contributo')
                    ->numeric(),
                TextInput::make('contributeFrequency')
                    ->label('Frequenza Contributo')
                    ->numeric()
                    ->default(1),
                DatePicker::make('contributeFrom')
                    ->label('Valido dal'),
                TextInput::make('remburse')
                    ->label('Rimborso')
                    ->numeric(),
                TextInput::make('vat_number')
                    ->label('Partita IVA'),
                TextInput::make('vat_name')
                    ->label('Ragione Sociale'),
                Toggle::make('is_active')
                    ->label('Attivo')
                    ->required(),
                Toggle::make('is_art108')
                    ->label('Esente art. 108 - ex art. 128-novies TUB')
                    ->helperText("Seleziona se l'agente è esente ai sensi dell'art. 108 del Testo Unico Bancario")
                    ->default(false),
            ]);
    }
}
