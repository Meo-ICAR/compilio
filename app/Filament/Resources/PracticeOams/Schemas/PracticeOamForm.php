<?php

namespace App\Filament\Resources\PracticeOams\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PracticeOamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id'),
                TextInput::make('practice_id')
                    ->numeric(),
                TextInput::make('oam_code_id')
                    ->numeric(),
                TextInput::make('compenso')
                    ->numeric(),
                TextInput::make('compenso_lavorazione')
                    ->numeric(),
                TextInput::make('compenso_premio')
                    ->numeric(),
                TextInput::make('compenso_rimborso')
                    ->numeric(),
                TextInput::make('compenso_assicurazione')
                    ->numeric(),
                TextInput::make('compenso_cliente')
                    ->numeric(),
                TextInput::make('storno')
                    ->numeric(),
                TextInput::make('provvigione')
                    ->numeric(),
                TextInput::make('provvigione_lavorazione')
                    ->numeric(),
                TextInput::make('provvigione_premio')
                    ->numeric(),
                TextInput::make('provvigione_rimborso')
                    ->numeric(),
                TextInput::make('provvigione_assicurazione')
                    ->numeric(),
                TextInput::make('provvigione_storno')
                    ->numeric(),
            ]);
    }
}
