<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Forms\Components\DatePicker;
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
                    ->required(),
                TextInput::make('description'),
                TextInput::make('oam'),
                DatePicker::make('oam_at'),
                TextInput::make('oam_name'),
                DatePicker::make('stipulated_at'),
                DatePicker::make('dismissed_at'),
                TextInput::make('type'),
                TextInput::make('contribute')
                    ->numeric(),
                TextInput::make('contributeFrequency')
                    ->numeric()
                    ->default(1),
                DatePicker::make('contributeFrom'),
                TextInput::make('remburse')
                    ->numeric(),
                TextInput::make('vat_number'),
                TextInput::make('vat_name'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('company_id')
                    ->required(),
            ]);
    }
}
