<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id')
                    ->required(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('name'),
                TextInput::make('role_title'),
                TextInput::make('cf'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('department'),
                TextInput::make('oam'),
                TextInput::make('ivass'),
                DatePicker::make('hiring_date'),
                DatePicker::make('termination_date'),
                TextInput::make('company_branche_id')
                    ->numeric(),
            ]);
    }
}
