<?php

namespace App\Filament\Resources\OAMSoggettis\Schemas;

use App\Services\ChecklistService;
use App\Services\GeminiVisionService;
use App\Traits\HasDocumentTypeFiltering;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OAMSoggettiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE DATI ANAGRAFICI
                Section::make('Dati Anagrafici')
                    ->description('Informazioni principali del soggetto OAM')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('denominazione_sociale')
                            ->label('Denominazione Sociale')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('es. Rossi Mario SRL')
                            ->helperText('Nome completo del soggetto'),
                        Select::make('persona')
                            ->label('Tipo Persona')
                            ->required()
                            ->options([
                                'Fisica' => 'Persona Fisica',
                                'Giuridica' => 'Persona Giuridica',
                            ])
                            ->default('Giuridica')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-adjust field labels based on person type
                                if ($state === 'Fisica') {
                                    $set('codice_fiscale_label', 'Codice Fiscale');
                                } else {
                                    $set('codice_fiscale_label', 'Codice Fiscale / P.IVA');
                                }
                            }),
                        TextInput::make('codice_fiscale')
                            ->label('Codice Fiscale / P.IVA')
                            ->required()
                            ->maxLength(16)
                            ->placeholder('es. RSSMRA85A01H501Z')
                            ->helperText('Codice fiscale per persona fisica o Partita IVA per giuridica')
                            ->unique(ignoreRecord: true, table: 'o_a_m_soggetti'),
                        TextInput::make('domicilio_sede_legale')
                            ->label('Domicilio / Sede Legale')
                            ->maxLength(255)
                            ->placeholder('es. Via Roma 1, 00100 Roma (RM)')
                            ->helperText('Indirizzo completo del domicilio o sede legale')
                            ->columnSpanFull(),
                    ]),
                // SEZIONE DATI OAM
                Section::make('Dati Iscrizione OAM')
                    ->description("Informazioni relative all'iscrizione nell'elenco OAM")
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        TextInput::make('elenco')
                            ->label('Elenco')
                            ->maxLength(100)
                            ->placeholder('es. Agenti in attività finanziaria')
                            ->helperText('Tipo di elenco OAM'),
                        TextInput::make('numero_iscrizione')
                            ->label('Numero Iscrizione')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('es. 12345')
                            ->helperText('Numero univoco di iscrizione')
                            ->unique(ignoreRecord: true, table: 'o_a_m_soggetti'),
                        DatePicker::make('data_iscrizione')
                            ->label('Data Iscrizione')
                            ->required()
                            ->placeholder('gg/mm/aaaa')
                            ->displayFormat('d/m/Y')
                            ->helperText("Data di iscrizione all'elenco OAM"),
                        Toggle::make('autorizzato_ad_operare')
                            ->label('Autorizzato ad Operare')
                            ->default(true)
                            ->helperText('Indica se il soggetto è autorizzato ad operare')
                            ->columnSpanFull(),
                    ]),
                // SEZIONE STATO
                Section::make('Stato del Soggetto')
                    ->description('Informazioni sullo stato attuale del soggetto')
                    ->icon('heroicon-o-flag')
                    ->columns(2)
                    ->schema([
                        Select::make('stato')
                            ->label('Stato')
                            ->required()
                            ->options([
                                'Attivo' => 'Attivo',
                                'Sospeso' => 'Sospeso',
                                'Cancellato' => 'Cancellato',
                                'Revocato' => 'Revocato',
                            ])
                            ->default('Attivo')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-set data_stato when state changes
                                if ($state !== 'Attivo') {
                                    $set('data_stato', now()->format('Y-m-d'));
                                }
                            })
                            ->helperText('Stato attuale del soggetto'),
                        DatePicker::make('data_stato')
                            ->label('Data Variazione Stato')
                            ->placeholder('gg/mm/aaaa')
                            ->displayFormat('d/m/Y')
                            ->helperText('Data in cui è cambiato lo stato')
                            ->visible(fn(callable $get) => $get('stato') !== 'Attivo'),
                        Textarea::make('causale_stato_note')
                            ->label('Note Causale Stato')
                            ->rows(3)
                            ->placeholder('Descrivi il motivo della variazione di stato...')
                            ->helperText('Note esplicative sulla variazione di stato')
                            ->visible(fn(callable $get) => $get('stato') !== 'Attivo')
                            ->columnSpanFull(),
                    ]),
                // SEZIONE COLLABORAZIONI
                Section::make('Collaborazioni')
                    ->description('Informazioni sulle collaborazioni attive')
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        TextInput::make('dipendente_collaboratore_di')
                            ->label('Dipendente/Collaboratore di')
                            ->maxLength(255)
                            ->placeholder('es. Banca S.p.A.')
                            ->helperText("Nome dell'ente per cui collabora")
                            ->columnSpanFull(),
                        TextInput::make('numero_collaborazioni_attive')
                            ->label('Numero Collaborazioni Attive')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Numero di collaborazioni attualmente attive'),
                        Toggle::make('check_collaborazione')
                            ->label('Verifica Collaborazione')
                            ->default(false)
                            ->helperText('Indica se è necessaria una verifica delle collaborazioni'),
                    ]),
                // SEZIONE INFORMAZIONI SISTEMA
                Section::make('Informazioni di Sistema')
                    ->description('Dati gestiti automaticamente dal sistema')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Data Creazione')
                            ->content(fn($record) => $record?->created_at?->format('d/m/Y H:i') ?? 'Nuovo record')
                            ->columnSpan(1),
                        Placeholder::make('updated_at')
                            ->label('Ultima Modifica')
                            ->content(fn($record) => $record?->updated_at?->format('d/m/Y H:i') ?? 'Non ancora salvato')
                            ->columnSpan(1),
                    ])
                    ->visible(fn($record) => $record && $record->exists),
            ]);
    }
}
