<?php

namespace App\Filament\Resources\Checklists\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // SEZIONE 1: Dettagli del Template
                Forms\Components\Section::make('Dettagli del Template Checklist')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome della Checklist')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipo di utilizzo')
                            ->options([
                                'loan_management' => 'Gestione Pratica / Finanziamento',
                                'audit' => 'Verifica Ispettiva / Audit',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('is_practice')
                                ->label('Riferita a una Pratica')
                                ->inline(false)
                                ->default(false),
                            Forms\Components\Toggle::make('is_audit')
                                ->label('Riferita a un Audit / Compliance')
                                ->inline(false)
                                ->default(false),
                        ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione generale')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                // SEZIONE 2: Domande / Items (Repeater)
                Forms\Components\Section::make('Domande ed Elementi della Checklist')
                    ->description("Trascina gli elementi per riordinarli. L'ordine verrà salvato automaticamente.")
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('checklistItems')  // Punta alla relazione HasMany nel modello Checklist
                            ->schema([
                                // Riga 1: Dati Base Domanda
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('item_code')
                                        ->label('Codice Univoco (es. doc_id, q1)')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('name')
                                        ->label('Titolo / Nome Breve')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                                Forms\Components\Textarea::make('question')
                                    ->label('Testo della Domanda / Richiesta')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label("Istruzioni per l'operatore (Opzionale)")
                                    ->rows(2)
                                    ->columnSpanFull(),
                                // Riga 2: Impostazioni Allegati e Obbligatorietà
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Toggle::make('is_required')
                                        ->label('Risposta Obbligatoria')
                                        ->inline(false)
                                        ->default(true),
                                    Forms\Components\Select::make('n_documents')
                                        ->label('Allegati Richiesti')
                                        ->options([
                                            0 => 'Nessun allegato (Solo risposta testo/vero-falso)',
                                            1 => 'Esattamente 1 Documento',
                                            99 => 'Documenti Multipli Consentiti',
                                        ])
                                        ->required()
                                        ->default(0)
                                        ->native(false)
                                        ->live(),
                                    Forms\Components\Select::make('attach_model')
                                        ->label('Modello di destinazione file')
                                        ->options([
                                            'principal' => 'Cliente (Principal)',
                                            'agent' => 'Agente / Collaboratore',
                                            'company' => 'Azienda',
                                            'audit' => 'Verifica Ispettiva (Audit)',
                                        ])
                                        ->native(false)
                                        // Mostra il campo solo se n_documents > 0
                                        ->visible(fn(Get $get) => $get('n_documents') > 0)
                                        ->required(fn(Get $get) => $get('n_documents') > 0),
                                ]),
                                // Riga 3: Logica Condizionale
                                Forms\Components\Section::make('Logica Condizionale')
                                    ->schema([
                                        Forms\Components\Select::make('dependency_type')
                                            ->label('Comportamento')
                                            ->options([
                                                'show_if' => 'Mostra questa domanda solo se...',
                                                'hide_if' => 'Nascondi questa domanda se...',
                                            ])
                                            ->native(false)
                                            ->live(),
                                        Forms\Components\TextInput::make('depends_on_code')
                                            ->label('Codice della domanda precedente')
                                            ->helperText('Inserisci il codice univoco della domanda da cui dipende')
                                            ->visible(fn(Get $get) => filled($get('dependency_type')))
                                            ->required(fn(Get $get) => filled($get('dependency_type'))),
                                        Forms\Components\TextInput::make('depends_on_value')
                                            ->label('Valore atteso')
                                            ->helperText('Es. 1 per Vero, 0 per Falso')
                                            ->visible(fn(Get $get) => filled($get('dependency_type')))
                                            ->required(fn(Get $get) => filled($get('dependency_type'))),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->collapsed(),  // Chiuso di default per mantenere pulita l'interfaccia
                            ])
                            ->orderColumn('ordine')  // Salva automaticamente l'ordine usando la colonna `ordine` (drag and drop)
                            ->defaultItems(1)
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Nuova Voce')
                            ->collapsible()
                            ->cloneable()  // Utile per duplicare domande simili (es. i documenti)
                            ->columnSpanFull()
                    ]),
            ]);
    }
}
