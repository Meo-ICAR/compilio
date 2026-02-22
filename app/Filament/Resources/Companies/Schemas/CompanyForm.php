<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('vat_number'),
                TextInput::make('vat_name'),
                TextInput::make('oam'),
                DatePicker::make('oam_at'),
                TextInput::make('oam_name'),
            ]);
    }
}
