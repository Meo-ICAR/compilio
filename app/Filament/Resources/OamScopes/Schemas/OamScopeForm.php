<?php

namespace App\Filament\Resources\OamScopes\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OamScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Codice OAM'),
                TextInput::make('name')
                    ->required()
                    ->label('Descrizione Ambito'),
                CheckboxList::make('tipo_prodotto')
                    ->label('Tipi Prodotto')
                    ->options(function () {
                        // Ottieni i valori distinct di tipo_prodotto da practice_oams
                        return \DB::table('practice_oams')
                            ->whereNotNull('tipo_prodotto')
                            ->where('tipo_prodotto', '!=', '')
                            ->distinct()
                            ->pluck('tipo_prodotto', 'tipo_prodotto')
                            ->sort()
                            ->toArray();
                    })
                    ->columns(3)
                    ->helperText('Seleziona i tipi prodotto associati a questo ambito OAM')
                    ->searchable()
                    ->bulkToggleable(),
            ]);
    }
}
