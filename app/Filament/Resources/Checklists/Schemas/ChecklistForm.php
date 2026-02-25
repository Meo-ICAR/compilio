<?php

namespace App\Filament\Resources\Checklists\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;

class ChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Generali')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Checklist')
                            ->required()
                            ->maxLength(255),
                        
                        Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'loan_management' => 'Gestione Prestiti',
                                'audit' => 'Audit/Compliance',
                            ])
                            ->required(),
                        
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->nullable(),
                        
                        Select::make('principal_id')
                            ->label('Principal Specifico')
                            ->relationship('principal', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2),
                
                Section::make('Configurazione')
                    ->schema([
                        Checkbox::make('is_practice')
                            ->label('Riferito a Pratiche')
                            ->default(false),
                        
                        Checkbox::make('is_audit')
                            ->label('Per Audit/Compliance')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }
}
