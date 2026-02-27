<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Client Details')
                    ->tabs([
                        // --- TAB 1: ANAGRAFICA ---
                        Tab::make('Anagrafica')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Grid::make(3)->schema([
                                    Toggle::make('is_person')
                                        ->label('Persona Fisica')
                                        ->default(true)
                                        ->live()  // Ricarica la form al cambio
                                        ->columnSpan(1),
                                    Select::make('status')
                                        ->options([
                                            'raccolta_dati' => 'Raccolta Dati',
                                            'valutazione_aml' => 'Valutazione AML',
                                            'approvata' => 'Approvata',
                                            'sos_inviata' => 'SOS Inviata',
                                            'chiusa' => 'Chiusa',
                                        ])
                                        ->required()
                                        ->columnSpan(2),
                                ]),
                                Section::make('Dati Identificativi')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(fn(Get $get) => $get('is_person') ? 'Cognome' : 'Ragione Sociale')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('first_name')
                                            ->label('Nome')
                                            ->visible(fn(Get $get) => $get('is_person'))  // Scompare se azienda
                                            ->maxLength(255),
                                        TextInput::make('tax_code')
                                            ->label('Codice Fiscale / P.IVA')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(16),
                                    ])
                                    ->columns(2),
                                Section::make('Contatti & Origine')
                                    ->schema([
                                        TextInput::make('email')->email(),
                                        TextInput::make('phone')->tel(),
                                        Select::make('client_type_id')
                                            ->relationship('clientType', 'name')
                                            ->searchable(),
                                        Select::make('leadsource_id')
                                            ->relationship('leadSource', 'name')
                                            ->label('Sorgente Lead')
                                            ->searchable(),
                                    ])
                                    ->columns(2),
                            ]),
                        // --- TAB 2: COMPLIANCE & PRIVACY ---
                        Tab::make('Compliance & Privacy')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Valutazione Rischio (AML)')
                                    ->description('Indicatori di rischio e posizioni critiche')
                                    ->schema([
                                        Toggle::make('is_pep')->label('PEP (Esposto Politicamente)'),
                                        Toggle::make('is_sanctioned')->label('Sanzionato / Blacklist'),
                                        Toggle::make('is_art108')
                                            ->label('Esente art. 108 - ex art. 128-novies TUB')
                                            ->helperText("Seleziona se il cliente è esente ai sensi dell'art. 108 del Testo Unico Bancario"),
                                        Toggle::make('is_remote_interaction')->label('Interazione a Distanza'),
                                    ])
                                    ->columns(3),
                                Section::make('Consensi Privacy')
                                    ->schema([
                                        DateTimePicker::make('general_consent_at')->label('Consenso Base'),
                                        DateTimePicker::make('consent_marketing_at')->label('Marketing'),
                                        DateTimePicker::make('consent_profiling_at')->label('Profilazione'),
                                        DateTimePicker::make('consent_sic_at')->label('Consenso SIC (CRIF)'),
                                    ])
                                    ->columns(2),
                            ]),
                        // --- TAB 3: DATI ECONOMICI E DOCUMENTI ---
                        Tab::make('Dati Finanziari & Doc')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('salary')
                                        ->numeric()
                                        ->prefix('€')
                                        ->label('Retribuzione Annuale (RAL)'),
                                    TextInput::make('salary_quote')
                                        ->numeric()
                                        ->step(0.01)
                                        ->label('Quota Cedibile/Calcolata'),
                                ]),
                                Section::make('Documentazione')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('documents')
                                            ->collection('client_documents')
                                            ->multiple()
                                            ->reorderable()
                                            ->label('Documenti Identità / Reddito')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        // --- TAB 4: AMMINISTRAZIONE ---
                        Tab::make('Admin / Stato')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Textarea::make('subfornitori')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Grid::make(3)->schema([
                                    Toggle::make('is_approved')->label('Approvato'),
                                    Toggle::make('is_anonymous')->label('Anonimizza'),
                                    Toggle::make('is_lead')->label('È un Lead'),
                                ]),
                                DateTimePicker::make('blacklist_at')
                                    ->label('Data Blacklist')
                                    ->readOnly(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
