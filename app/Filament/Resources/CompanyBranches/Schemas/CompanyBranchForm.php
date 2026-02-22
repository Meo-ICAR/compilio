<?php

namespace App\Filament\Resources\CompanyBranches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyBranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Toggle::make('is_main_office')
                    ->required(),
                TextInput::make('manager_first_name'),
                TextInput::make('manager_last_name'),
                TextInput::make('manager_tax_code'),
            ]);
    }
}
