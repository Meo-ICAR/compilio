<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('requester_type')
                    ->options([
                        'OAM' => 'O a m',
                        'PRINCIPAL' => 'P r i n c i p a l',
                        'INTERNAL' => 'I n t e r n a l',
                        'EXTERNAL' => 'E x t e r n a l',
                    ])
                    ->required(),
                Select::make('principal_id')
                    ->label('Mandante')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->nullable(),
                Select::make('agent_id')
                    ->label('Agente')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->nullable(),
                Select::make('regulatory_body_id')
                    ->label('Ente Regolatore')
                    ->relationship('regulatoryBody', 'name')
                    ->searchable()
                    ->nullable(),
                Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->nullable(),
                TextInput::make('title')
                    ->label('Titolo Audit')
                    ->required(),
                TextInput::make('emails')
                    ->label('Email Notifiche')
                    ->email()
                    ->required(),
                TextInput::make('reference_period')
                    ->label('Periodo di Riferimento'),
                DatePicker::make('start_date')
                    ->label('Data Inizio')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Data Fine'),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'PROGRAMMATO' => 'P r o g r a m m a t o',
                        'IN_CORSO' => 'I n  c o r s o',
                        'COMPLETATO' => 'C o m p l e t a t o',
                        'ARCHIVIATO' => 'A r c h i v i a t o',
                    ])
                    ->default('PROGRAMMATO'),
                TextInput::make('overall_score')
                    ->label('Valutazione Finale'),
            ]);
    }
}
