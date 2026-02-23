<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nominativo'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->tel(),
                Select::make('role_title')
                    ->label('Ruolo')
                    ->options([
                        'Amministratore' => 'Amministratore',
                        'Operatore' => 'Operatore',
                        'Consulente' => 'Consulente',
                    ]),
                Select::make('company_branch_id')
                    ->label('Sede')
                    ->relationship('companyBranch', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('department')
                    ->label('Dipartimento'),
                Select::make('employment_type_id')
                    ->label('Tipo di Impiego')
                    ->relationship('employmentType', 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('hire_date')
                    ->label('Data Assunzione'),
                DatePicker::make('termination_date')
                    ->label('Data Cessazione'),
            ]);
    }
}
