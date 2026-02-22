<?php

namespace App\Filament\Resources\TrainingRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainingRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('training_session_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
            'ISCRITTO' => 'I s c r i t t o',
            'FREQUENTANTE' => 'F r e q u e n t a n t e',
            'COMPLETATO' => 'C o m p l e t a t o',
            'NON_SUPERATO' => 'N o n  s u p e r a t o',
        ])
                    ->default('ISCRITTO'),
                TextInput::make('name'),
                TextInput::make('hours_attended')
                    ->numeric()
                    ->default(0.0),
                TextInput::make('score'),
                DatePicker::make('completion_date'),
                TextInput::make('certificate_path'),
            ]);
    }
}
