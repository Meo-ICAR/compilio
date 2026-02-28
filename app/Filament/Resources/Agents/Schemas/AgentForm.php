<?php

namespace App\Filament\Resources\Agents\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. SEZIONE ANAGRAFICA E STATUS
                Section::make('Anagrafica e Inquadramento')
                    ->description('Dati principali e collegamento utente del collaboratore.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome / Denominazione')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Select::make('type')
                                ->label('Tipologia Collaboratore')
                                ->options([
                                    'Agente' => 'Agente',
                                    'Mediatore' => 'Mediatore',
                                    'Consulente' => 'Consulente',
                                    'Call Center' => 'Call Center',
                                ])
                                ->searchable(),
                            Toggle::make('is_active')
                                ->label('Attivo/Convenzionato')
                                ->default(true)
                                ->inline(false),
                            Select::make('user_id')
                                ->label('Utente di Sistema Collegato')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText("Associa questo profilo a un account per l'accesso al CRM."),
                            // company_id solitamente si gestisce in background col multi-tenancy,
                            // ma se serve selezionarlo a mano:
                            Select::make('company_id')
                                ->label('Agenzia Proprietaria')
                                ->relationship('company', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                        Textarea::make('description')
                            ->label('Note / Descrizione interna')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                // 2. SEZIONE NORMATIVA E OAM
                Section::make('Dati OAM e Mandato')
                    ->description("Estremi di iscrizione all'elenco e date di validità del contratto.")
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('oam')
                                ->label('Numero Iscrizione OAM')
                                ->maxLength(30),
                            TextInput::make('oam_name')
                                ->label('Denominazione registrata in OAM')
                                ->maxLength(255),
                            DatePicker::make('oam_at')
                                ->label('Data Iscrizione OAM')
                                ->displayFormat('d/m/Y'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('stipulated_at')
                                ->label('Data Inizio Mandato')
                                ->displayFormat('d/m/Y')
                                ->required(),
                            DatePicker::make('dismissed_at')
                                ->label('Data Cessazione Rapporto')
                                ->displayFormat('d/m/Y')
                                ->helperText('Compilare solo in caso di interruzione del rapporto.'),
                        ]),
                    ]),
                // 3. SEZIONE FISCALE ED ENASARCO
                Section::make('Fiscale ed Enasarco')
                    ->description('Dati per la fatturazione e inquadramento previdenziale.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('vat_name')
                                ->label('Ragione Sociale Fiscale')
                                ->maxLength(255),
                            TextInput::make('vat_number')
                                ->label('Partita IVA / Codice Fiscale')
                                ->maxLength(16),
                            Select::make('enasarco')
                                ->label('Posizione Enasarco')
                                ->options([
                                    'no' => 'Non soggetto',
                                    'monomandatario' => 'Monomandatario',
                                    'plurimandatario' => 'Plurimandatario',
                                    'societa' => 'Società di Capitali',
                                ])
                                ->default('no'),
                            TextInput::make('contoCOGE')
                                ->label('Conto COGE (Contabilità)')
                                ->maxLength(255)
                                ->helperText("Codice conto per l'esportazione in contabilità."),
                        ]),
                    ]),
                // 4. SEZIONE CONTRIBUTI E RIMBORSI
                Section::make('Condizioni Economiche Fisse')
                    ->description('Fee fisse mensili, rimborsi e addebiti ricorrenti (Desk, CRM, ecc.).')
                    ->icon('heroicon-o-currency-euro')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('contribute')
                                ->label('Addebito Fisso (Desk/CRM)')
                                ->numeric()
                                ->prefix('€')
                                ->maxValue(99999999.99),
                            TextInput::make('contributeFrequency')
                                ->label('Frequenza addebito (Mesi)')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(12),
                            DatePicker::make('contributeFrom')
                                ->label('Inizio addebito dal')
                                ->displayFormat('d/m/Y'),
                            TextInput::make('remburse')
                                ->label('Rimborso Spese Fisso Mensile')
                                ->numeric()
                                ->prefix('€')
                                ->maxValue(99999999.99)
                                ->columnSpan(3),
                        ]),
                    ]),
            ]);
    }
}
