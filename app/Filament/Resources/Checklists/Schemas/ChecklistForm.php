<?php

namespace App\Filament\Resources\Checklists\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Get;

class ChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE PRINCIPALE
                Section::make('Informazioni Generali')
                    ->description('Dati principali della checklist')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome Checklist')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Nome univoco della checklist'),
                            TextInput::make('code')
                                ->label('Codice')
                                ->maxLength(50)
                                ->helperText('Codice univoco per riferimento rapido'),
                        ]),
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->helperText('Descrizione dettagliata della checklist'),
                    ]),
                // SEZIONE TIPOLOGIA
                Section::make('Tipologia Checklist')
                    ->description('Configurazione tipologia e destinazione')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('checklist_type_id')
                                ->label('Tipo Checklist')
                                ->relationship('checklistType', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText('Seleziona il tipo di checklist'),
                            Select::make('type')
                                ->label('Tipo')
                                ->options([
                                    'practice' => 'Pratica',
                                    'agent' => 'Agente',
                                    'company' => 'Azienda',
                                    'principal' => 'Mandante',
                                    'general' => 'Generale',
                                ])
                                ->default('general')
                                ->helperText('Tipologia principale della checklist'),
                        ]),
                        Grid::make(3)->schema([
                            Toggle::make('is_practice')
                                ->label('Pratica')
                                ->default(false)
                                ->helperText('Checklist per pratiche'),
                            Toggle::make('is_audit')
                                ->label('Audit')
                                ->default(false)
                                ->helperText('Checklist di audit'),
                            Toggle::make('is_template')
                                ->label('Template')
                                ->default(false)
                                ->helperText('Checklist modello riutilizzabile'),
                        ]),
                        Toggle::make('is_unique')
                            ->label('Unica')
                            ->default(false)
                            ->helperText('Checklist unica per target (non duplicabile)'),
                    ]),
                // SEZIONE ASSOCIAZIONI
                Section::make('Associazioni')
                    ->description('Collegamenti con altre entità')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('principal_id')
                                ->label('Mandante')
                                ->relationship('principal', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Mandante associato'),
                            Select::make('document_type_id')
                                ->label('Tipo Documento')
                                ->relationship('documentType', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Tipo documento associato'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('document_id')
                                ->label('Documento')
                                ->relationship('document', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Documento specifico associato'),
                            Select::make('business_function_id')
                                ->label('Funzione Business')
                                ->relationship('businessFunction', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Funzione business associata'),
                        ]),
                    ]),
                // SEZIONE GESTIONE
                Section::make('Gestione')
                    ->description('Informazioni gestione e stato')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label('Assegnato a')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Utente responsabile della checklist'),
                            Select::make('status')
                                ->label('Stato')
                                ->options([
                                    'draft' => 'Bozza',
                                    'active' => 'Attiva',
                                    'completed' => 'Completata',
                                    'suspended' => 'Sospesa',
                                    'archived' => 'Archiviata',
                                ])
                                ->default('draft')
                                ->helperText('Stato corrente della checklist'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('duration')
                                ->label('Durata (giorni)')
                                ->numeric()
                                ->default(30)
                                ->helperText('Durata prevista in giorni'),
                            TextInput::make('richiedente')
                                ->label('Richiedente')
                                ->maxLength(255)
                                ->helperText('Nome del richiedente'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('received_at')
                                ->label('Data Ricezione')
                                ->helperText('Data di ricezione richiesta'),
                            DatePicker::make('sended_at')
                                ->label('Data Invio')
                                ->helperText('Data di invio/completamento'),
                        ]),
                        TextInput::make('protocollo')
                            ->label('Protocollo')
                            ->maxLength(100)
                            ->helperText('Numero protocollo'),
                        Textarea::make('annotation')
                            ->label('Annotazioni')
                            ->rows(3)
                            ->helperText('Note interne sulla checklist'),
                    ]),
                // SEZIONE ALLEGATI
                Section::make('Allegati')
                    ->description('Documenti e file allegati')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('File Allegati')
                            ->multiple()
                            ->reorderable()
                            ->helperText('Carica documenti relativi alla checklist'),
                    ])
                    ->collapsible(),
            ]);
    }
}
