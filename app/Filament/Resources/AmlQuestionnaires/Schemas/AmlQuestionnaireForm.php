<?php

namespace App\Filament\Resources\AmlQuestionnaires\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms;

class AmlQuestionnaireForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Esito Questionario')
                    ->schema([
                        Forms\Components\Section::make('Compilazione Questionario AML')
                            ->description('Rispondi alle seguenti domande per calcolare il profilo di rischio.')
                            // MAGIA: Tutto quello che è in questa section verrà salvato come JSON nella colonna qna_payload
                            ->statePath('qna_payload')
                            ->schema([
                                Forms\Components\Select::make('scopo_rapporto')
                                    ->label('1. Qual è lo scopo e la natura del rapporto?')
                                    ->options([
                                        'finanziamento_personale' => 'Finanziamento per esigenze personali/familiari',
                                        'acquisto_immobile' => 'Acquisto bene immobile',
                                        'liquidita_aziendale' => "Liquidità per attività d'impresa",
                                        'consolidamento_debiti' => 'Consolidamento debiti',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('origine_fondi')
                                    ->label("2. Qual è l'origine dei fondi/patrimonio del cliente?")
                                    ->options([
                                        'stipendio' => 'Stipendio / Lavoro dipendente',
                                        'reddito_impresa' => "Reddito d'impresa / Lavoro autonomo",
                                        'risparmi' => 'Risparmi pregressi',
                                        'eredita' => 'Eredità / Donazione',
                                    ])
                                    ->required(),
                                Forms\Components\Radio::make('pep')
                                    ->label('3. Il cliente è una Persona Politicamente Esposta (PEP)?')
                                    ->boolean()
                                    ->inline()
                                    ->required(),
                                Forms\Components\Select::make('comportamento_cliente')
                                    ->label("4. Comportamento tenuto dal cliente durante l'identificazione")
                                    ->options([
                                        'collaborativo' => 'Collaborativo e trasparente',
                                        'riluttante' => 'Riluttante a fornire informazioni',
                                        'incoerente' => 'Fornisce informazioni palesemente incoerenti',
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('note_aggiuntive')
                                    ->label("Note aggiuntive dell'operatore (Opzionale)")
                                    ->rows(2),
                            ])
                            ->columns(2),
                        Forms\Components\TextInput::make('calculated_score')
                            ->label('Punteggio Rischio')
                            ->numeric()
                            ->readOnly(),  // Solitamente calcolato dal sistema, non editabile a mano
                        Forms\Components\Select::make('risk_level')
                            ->label('Livello di Rischio AML')
                            ->options([
                                'basso' => 'Basso',
                                'medio' => 'Medio',
                                'alto' => 'Alto',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('Valido Fino Al')
                            ->required()
                            ->default(now()->addYears(3)),  // Es. Scadenza standard a 3 anni
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Archivio Documentale (GDPR)')
                    ->schema([
                        // IL COMPONENTE MAGICO DI SPATIE MEDIA LIBRARY
                        Forms\Components\SpatieMediaLibraryFileUpload::make('documento_firmato')
                            ->collection('documento_firmato')  // Deve coincidere col nome nel Model
                            ->label('Scansione Questionario Firmato (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)  // Max 5MB
                            ->downloadable()
                            ->columnSpanFull()
                            ->helperText('Carica qui il documento firmato dal cliente. Il file sarà archiviato in un volume protetto non accessibile dal web.'),
                    ]),
            ]);
    }
}
