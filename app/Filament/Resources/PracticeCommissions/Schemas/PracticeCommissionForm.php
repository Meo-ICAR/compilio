<?php

namespace App\Filament\Resources\PracticeCommissions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeCommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('practice_id')
                    ->required()
                    ->numeric(),
                TextInput::make('proforma_id')
                    ->numeric(),
                TextInput::make('agent_id')
                    ->numeric(),
                TextInput::make('principal_id')
                    ->numeric(),
                TextInput::make('amount')
                    ->numeric(),
                TextInput::make('description'),
                DatePicker::make('perfected_at'),
                Toggle::make('is_coordination'),
                DatePicker::make('cancellation_at'),
                TextInput::make('invoice_number'),
                DatePicker::make('invoice_at'),
                DatePicker::make('paided_at'),
                Toggle::make('is_storno'),
                Toggle::make('is_enasarco'),
            ]);
    }
}
