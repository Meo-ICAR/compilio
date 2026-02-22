<?php

namespace App\Filament\Resources\Practices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id')
                    ->required(),
                TextInput::make('client_id')
                    ->required()
                    ->numeric(),
                TextInput::make('principal_id')
                    ->required()
                    ->numeric(),
                TextInput::make('bank_id')
                    ->required()
                    ->numeric(),
                TextInput::make('agent_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('CRM_code')
                    ->required(),
                TextInput::make('principal_code')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('net')
                    ->required()
                    ->numeric(),
                TextInput::make('practice_scope_id')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('istruttoria'),
                DatePicker::make('perfected_at')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
