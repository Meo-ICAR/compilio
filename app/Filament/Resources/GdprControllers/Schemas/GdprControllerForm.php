<?php

namespace App\Filament\Resources\GdprControllers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GdprControllerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                TextInput::make('vat_number'),
                TextInput::make('representative_name'),
                TextInput::make('dpo_name'),
                TextInput::make('dpo_email')
                    ->email(),
                DateTimePicker::make('version_at'),
            ]);
    }
}
