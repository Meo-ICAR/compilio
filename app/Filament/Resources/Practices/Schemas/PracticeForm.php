<?php

namespace App\Filament\Resources\Practices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PracticeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Principali')
                    ->description('Dati identificativi della pratica')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Pratica')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('es. Mutuo Acquisto Prima Casa Rossi')
                            ->helperText('Nome identificativo della pratica'),
                        TextInput::make('CRM_code')
                            ->label('Codice CRM')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('es. FVI25-00185')
                            ->helperText('Codice CRM univoco'),
                        Select::make('client_id')
                            ->label('Cliente')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleziona cliente')
                            ->helperText('Cliente associato alla pratica')
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\Client::where('name', 'like', "%{$search}%")
                                    ->orWhere('first_name', 'like', "%{$search}%")
                                    ->orWhere('tax_code', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $client = \App\Models\Client::find($value);
                                return $client ? $client->name . ' (' . $client->tax_code . ')' : $value;
                            }),
                        Select::make('client_mandate_id')
                            ->label('Mandato Cliente')
                            ->relationship('clientMandate', 'id')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Seleziona mandato')
                            ->helperText('Mandato del cliente associato alla pratica'),
                    ]),
                Section::make('Dettagli Pratica')
                    ->description('Informazioni dettagliate sulla pratica')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        Select::make('principal_id')
                            ->label('Mandante')
                            ->relationship('principal', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleziona mandante')
                            ->helperText('Banca o istituto finanziario'),
                        Select::make('agent_id')
                            ->label('Agente')
                            ->relationship('agent', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleziona agente')
                            ->helperText('Agente o collaboratore'),
                        Select::make('practice_status_id')
                            ->label('Stato Pratica')
                            ->relationship('practiceStatus', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Seleziona stato')
                            ->helperText('Stato attuale della pratica'),
                        TextInput::make('stato_pratica')
                            ->label('Stato Pratica Originale')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('es. In Istruttoria')
                            ->helperText('Stato pratica originale da sistema esterno'),
                        TextInput::make('principal_code')
                            ->label('Codice Mandante')
                            ->maxLength(50)
                            ->nullable()
                            ->placeholder('es. 01030')
                            ->helperText('Codice identificativo del mandante'),
                        TextInput::make('tipo_prodotto')
                            ->label('Tipo Prodotto')
                            ->maxLength(255)
                            ->nullable()
                            ->placeholder('es. Mutuo Casa')
                            ->helperText('Tipo prodotto CRM'),
                        Select::make('practice_scope_id')
                            ->label('Ambito')
                            ->relationship('practiceScope', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleziona ambito')
                            ->helperText('Ambito della pratica'),
                        Select::make('practice_id')
                            ->label('Pratica Collegata')
                            ->relationship('parentPractice', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Seleziona pratica principale')
                            ->helperText('Pratica principale a cui questa è collegata')
                            ->columnSpanFull()
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\Practice::where('name', 'like', "%{$search}%")
                                    ->orWhere('CRM_code', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $practice = \App\Models\Practice::find($value);
                                return $practice ? $practice->name . ' (' . $practice->CRM_code . ')' : $value;
                            }),
                    ]),
                Section::make('Importi e Provvigioni')
                    ->description('Valori finanziari della pratica')
                    ->icon('heroicon-o-currency-euro')
                    ->columns(3)
                    ->schema([
                        TextInput::make('amount')
                            ->label('Importo Richiesto')
                            ->numeric()
                            ->prefix('€')
                            ->placeholder('0,00')
                            ->helperText('Importo del finanziamento richiesto')
                            ->formatStateUsing(fn($state) => $state ? number_format($state, 2, ',', '.') : ''),
                        TextInput::make('net')
                            ->label('Netto Erogato')
                            ->numeric()
                            ->prefix('€')
                            ->placeholder('0,00')
                            ->helperText('Importo netto erogato')
                            ->formatStateUsing(fn($state) => $state ? number_format($state, 2, ',', '.') : ''),
                        Placeholder::make('importo_diff')
                            ->label('Differenza')
                            ->content(function ($get) {
                                $amount = floatval(str_replace(['.', ','], ['', '.'], $get('amount') ?? 0));
                                $net = floatval(str_replace(['.', ','], ['', '.'], $get('net') ?? 0));
                                $diff = $amount - $net;
                                return '€ ' . number_format($diff, 2, ',', '.');
                            })
                            ->helperText('Differenza tra importo e netto'),
                    ]),
                Fieldset::make('Provvigioni')
                    ->columns(3)
                    ->schema([
                        TextInput::make('brokerage_fee')
                            ->label('Provvigione')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->placeholder('0,00')
                            ->helperText('Provvigione pattuita'),
                        TextInput::make('principal_fee')
                            ->label('Provvigione Mandante')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->placeholder('0,00')
                            ->helperText('Provvigione mandante'),
                        TextInput::make('client_fee')
                            ->label('Provvigione Cliente')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->placeholder('0,00')
                            ->helperText('Provvigione cliente'),
                        TextInput::make('prize_fee')
                            ->label('Provvigione Assicurativa')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->placeholder('0,00')
                            ->helperText('Provvigione assicurativa'),
                        TextInput::make('insurance_fee')
                            ->label('Provvigione Assicurazione')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->placeholder('0,00')
                            ->helperText('Provvigione assicurazione'),
                        Placeholder::make('totale_provvigioni')
                            ->label('Totale Provvigioni')
                            ->content(function ($get) {
                                $fees = [
                                    'brokerage_fee', 'principal_fee', 'client_fee',
                                    'prize_fee', 'insurance_fee'
                                ];
                                $total = 0;
                                foreach ($fees as $fee) {
                                    $value = floatval(str_replace(['.', ','], ['', '.'], $get($fee) ?? 0));
                                    $total += $value;
                                }
                                return '€ ' . number_format($total, 2, ',', '.');
                            })
                            ->helperText('Somma di tutte le provvigioni'),
                    ]),
                Section::make('Stati della Pratica')
                    ->description('Stati e fasi della pratica')
                    ->icon('heroicon-o-flag')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Stato Interno')
                            ->options([
                                'working' => 'In Lavorazione',
                                'rejected' => 'Respinta',
                                'perfected' => 'Perfezionata',
                            ])
                            ->default('working')
                            ->helperText('Stato interno attuale della pratica')
                            ->required(),
                        Select::make('statoproforma')
                            ->label('Stato Proforma')
                            ->options([
                                'Inserito' => 'Inserito',
                                'Sospeso' => 'Sospeso',
                                'Annullato' => 'Annullato',
                                'Inviato' => 'Inviato',
                                'Abbinato' => 'Abbinato',
                            ])
                            ->nullable()
                            ->placeholder('Seleziona stato proforma')
                            ->helperText('Stato proforma della pratica'),
                    ]),
                Section::make('Date Importanti')
                    ->description('Timeline della pratica')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('inserted_at')
                            ->label('Data Inserimento')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data inserimento pratica'),
                        DatePicker::make('sended_at')
                            ->label('Data Invio Istruttoria')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data invio in istruttoria'),
                        DatePicker::make('approved_at')
                            ->label('Data Approvazione')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data approvazione pratica'),
                        DatePicker::make('erogated_at')
                            ->label('Data Erogazione')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data erogazione finanziamento / stipula mutuo'),
                        DatePicker::make('rejected_at')
                            ->label('Data Rifiuto')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data rifiuto pratica'),
                        DatePicker::make('status_at')
                            ->label('Data Stato')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data stato perfezionata'),
                        DatePicker::make('perfected_at')
                            ->label('Data Perfezionamento')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data perfezionamento pratica'),
                        DatePicker::make('invoice_at')
                            ->label('Data Fatturazione')
                            ->nullable()
                            ->placeholder('gg/mm/aaaa')
                            ->helperText('Data fatturazione della pratica'),
                    ]),
                Section::make('Note e Descrizioni')
                    ->description('Informazioni aggiuntive sulla pratica')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descrizione Pratica')
                            ->rows(3)
                            ->placeholder('Inserisci una descrizione dettagliata della pratica...')
                            ->helperText('Descrizione completa della pratica'),
                        Textarea::make('rejected_reason')
                            ->label('Causale Rifiuto')
                            ->rows(2)
                            ->nullable()
                            ->placeholder('es. Rifiutata dalla banca per insufficiente reddito')
                            ->helperText('Causale del rifiuto, se applicabile')
                            ->visible(fn($get) => $get('status') === 'rejected' || !empty($get('rejected_at'))),
                        Textarea::make('annotation')
                            ->label('Annotazioni Interne')
                            ->rows(3)
                            ->placeholder('Note interne per il team...')
                            ->helperText('Annotazioni interne sulla pratica'),
                    ]),
                Section::make('Impostazioni')
                    ->description('Configurazioni e opzioni della pratica')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Pratica Attiva')
                            ->default(true)
                            ->helperText('Indica se la pratica è attiva'),
                        Toggle::make('is_convenctioned')
                            ->label('Pratica Convenzionata')
                            ->default(true)
                            ->helperText('Indica se la pratica è convenzionata'),
                        Toggle::make('is_notowned')
                            ->label('Pratica di Terzi')
                            ->default(false)
                            ->helperText('Indica se la pratica non è di proprietà'),
                    ]),
            ]);
    }
}
