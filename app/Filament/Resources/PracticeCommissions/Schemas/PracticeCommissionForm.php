<?php

namespace App\Filament\Resources\PracticeCommissions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeCommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('practice_id')
                    ->relationship('practice', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('proforma_id')
                    ->relationship('proforma', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('agent_id')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('principal_id')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload(),
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
