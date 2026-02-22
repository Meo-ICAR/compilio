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
                TextInput::make('principal_id')
                    ->numeric(),
                TextInput::make('agent_id')
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('emails')
                    ->email()
                    ->required(),
                TextInput::make('reference_period'),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Select::make('status')
                    ->options([
            'PROGRAMMATO' => 'P r o g r a m m a t o',
            'IN_CORSO' => 'I n  c o r s o',
            'COMPLETATO' => 'C o m p l e t a t o',
            'ARCHIVIATO' => 'A r c h i v i a t o',
        ])
                    ->default('PROGRAMMATO'),
                TextInput::make('overall_score'),
            ]);
    }
}
