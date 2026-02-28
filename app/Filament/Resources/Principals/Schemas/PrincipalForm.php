<?php

namespace App\Filament\Resources\Principals\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class PrincipalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. SEZIONE ANAGRAFICA E TIPOLOGIA
                Section::make('Anagrafica Istituto')
                    ->description('Dati principali della banca o finanziaria.')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name')
                                ->label('Nome Istituto / Finanziaria')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Select::make('type')
                                ->label('Tipologia')
                                ->options([
                                    'Banca' => 'Banca',
                                    'Finanziaria' => 'Finanziaria',
                                    'Assicurazione' => 'Assicurazione',
                                    'Utility' => 'Utility',
                                ])
                                ->searchable(),
                        ]),
                        Grid::make(2)->schema([
                            Toggle::make('is_active')
                                ->label('Convenzione Attiva')
                                ->default(true)
                                ->inline(false)
                                ->helperText('Disattiva per nascondere questo istituto dalle nuove pratiche.'),
                            Toggle::make('is_dummy')
                                ->label('Istituto Fittizio (Non Convenzionato)')
                                ->default(false)
                                ->inline(false)
                                ->helperText('Usa per censire banche concorrenti o per estinzioni debiti terzi.'),
                        ]),
                    ]),
                // 2. SEZIONE DATI FISCALI E REGOLAMENTARI (OAM/IVASS)
                Section::make('Codici e Dati Fiscali')
                    ->description('Identificativi fiscali e iscrizioni agli albi di vigilanza.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('vat_name')
                                ->label('Ragione Sociale Fiscale')
                                ->maxLength(13),  // Attenzione: Consigliato modificare il DB a 255
                            TextInput::make('vat_number')
                                ->label('Partita IVA')
                                ->maxLength(13),
                            TextInput::make('abi')
                                ->label('Codice ABI / ISVASS')
                                ->maxLength(5)
                                ->helperText('5 cifre per le banche.'),
                            TextInput::make('contoCOGE')
                                ->label('Conto COGE (Contabilità)')
                                ->maxLength(255),
                            TextInput::make('oam')
                                ->label('Codice Iscrizione OAM')
                                ->maxLength(30),
                            TextInput::make('ivass')
                                ->label('Codice Iscrizione IVASS')
                                ->maxLength(30),
                        ]),
                    ]),
                // 3. SEZIONE DATI DEL MANDATO
                Section::make('Dettagli Mandato e Convenzione')
                    ->description("Estremi del contratto stipulato tra l'Agenzia e l'Istituto.")
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('mandate_number')
                                ->label('Protocollo / N° Mandato')
                                ->maxLength(100)
                                ->columnSpan(2),
                            Select::make('status')
                                ->label('Stato Operativo')
                                ->options([
                                    'ATTIVO' => 'Attivo',
                                    'SCADUTO' => 'Scaduto',
                                    'RECEDUTO' => 'Receduto',
                                    'SOPESO' => 'Sospeso',  // Mantiene il valore DB SOPESO ma lo mostra corretto
                                ])
                                ->default('ATTIVO')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('stipulated_at')
                                ->label('Data Stipula Convenzione')
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('dismissed_at')
                                ->label('Data Cessazione Convenzione')
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('start_date')
                                ->label('Decorrenza Mandato Attuale')
                                ->displayFormat('d/m/Y')
                                ->helperText("Inizio dell'ultimo rinnovo."),
                            DatePicker::make('end_date')
                                ->label('Scadenza Mandato')
                                ->displayFormat('d/m/Y')
                                ->helperText('Lascia vuoto se a tempo indeterminato.'),
                        ]),
                        Toggle::make('is_exclusive')
                            ->label('Mandato in Esclusiva')
                            ->default(false)
                            ->inline(false)
                            ->helperText("Attiva se c'è un patto di non concorrenza per questa categoria di prodotti."),
                    ]),
                // 4. SEZIONE NOTE E ACCORDI
                Section::make('Accordi Commerciali')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Note su provvigioni, premi di produzione o patti specifici')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
