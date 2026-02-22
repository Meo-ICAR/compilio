<?php

namespace App\Filament\Resources\TrainingSessions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainingSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('training_template_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('total_hours')
                    ->required()
                    ->numeric(),
                TextInput::make('trainer_name'),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                Select::make('location')
                    ->options(['ONLINE' => 'O n l i n e', 'PRESENZA' => 'P r e s e n z a', 'IBRIDO' => 'I b r i d o'])
                    ->default('ONLINE'),
            ]);
    }
}
