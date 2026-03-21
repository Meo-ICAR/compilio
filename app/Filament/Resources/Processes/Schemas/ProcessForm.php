<?php

namespace App\Filament\Resources\Processes\Schemas;

use App\Filament\Resources\Processes\RelationManagers\ProcessTasksRelationManager;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ProcessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Generali')
                    ->description('Definisci le informazioni principali del processo.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Processo')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Es: "Istruttoria Pratica", "Revisione Annuale KYC"'),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Identificativo univoco per URL (generato automaticamente)'),
                        TextInput::make('groupcode')
                            ->label('Codice Gruppo')
                            ->nullable()
                            ->maxLength(50)
                            ->helperText('Codice per raggruppare processi simili'),
                    ])
                    ->columns(2),
                Section::make('Configurazione')
                    ->description('Imposta la periodicità e lo stato del processo.')
                    ->schema([
                        Select::make('periodicity')
                            ->label('Periodicità')
                            ->options([
                                'once' => 'Una Tantum',
                                'monthly' => 'Mensile',
                                'quarterly' => 'Trimestrale',
                                'semiannual' => 'Semestrale',
                                'annual' => 'Annuale',
                            ])
                            ->default('once')
                            ->required()
                            ->helperText('Frequenza con cui il processo deve essere eseguito'),
                        Toggle::make('is_active')
                            ->label('Processo Attivo')
                            ->default(true)
                            ->helperText('Solo i processi attivi possono essere eseguiti'),
                    ])
                    ->columns(2),
            ]);
    }
}
