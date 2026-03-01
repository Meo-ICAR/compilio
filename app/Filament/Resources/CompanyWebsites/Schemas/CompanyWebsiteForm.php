<?php

namespace App\Filament\Resources\CompanyWebsites\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CompanyWebsiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE PRINCIPALE
                Section::make('Informazioni Generali')
                    ->description('Dati principali del sito web')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->label('Nome Sito'),
                            TextInput::make('domain')
                                ->required()
                                ->label('Dominio')
                                ->helperText('es. agenzia-x.mediaconsulence.it'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('type')
                                ->label('Tipologia')
                                ->helperText('Vetrina, Portale, Landing'),
                            Select::make('principal_id')
                                ->label('Mandante')
                                ->relationship('principal', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Per landing dedicate'),
                        ]),
                        Grid::make(2)->schema([
                            Toggle::make('is_active')
                                ->label('Sito Attivo')
                                ->default(true),
                            Toggle::make('is_typical')
                                ->label('Sito per Attività Tipica')
                                ->default(true)
                                ->helperText('Sito utilizzato per attività tipica'),
                        ]),
                    ]),
                // SEZIONE PRIVACY E TRASPARENZA
                Section::make('Privacy e Trasparenza')
                    ->description('Date di aggiornamento per documenti di privacy e trasparenza')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('privacy_date')
                                ->label('Data Aggiornamento Privacy')
                                ->displayFormat('d/m/Y')
                                ->helperText('Ultimo aggiornamento privacy policy'),
                            DatePicker::make('transparency_date')
                                ->label('Data Aggiornamento Trasparenza')
                                ->displayFormat('d/m/Y')
                                ->helperText('Ultimo aggiornamento informativa trasparenza'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('privacy_prior_date')
                                ->label('Precedente Aggiornamento Privacy')
                                ->displayFormat('d/m/Y')
                                ->helperText('Data del precedente aggiornamento privacy'),
                            DatePicker::make('transparency_prior_date')
                                ->label('Precedente Aggiornamento Trasparenza')
                                ->displayFormat('d/m/Y')
                                ->helperText('Data del precedente aggiornamento trasparenza'),
                        ]),
                    ]),
                // SEZIONE LINK E CONFORMITÀ
                Section::make('Link e Conformità')
                    ->description('URL delle pagine legali e stato conformità')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('url_privacy')
                                ->label('URL Privacy Policy')
                                ->url()
                                ->nullable()
                                ->prefix('https://')
                                ->helperText('Link alla pagina privacy policy'),
                            TextInput::make('url_cookies')
                                ->label('URL Cookie Policy')
                                ->url()
                                ->nullable()
                                ->prefix('https://')
                                ->helperText('Link alla pagina cookie policy'),
                        ]),
                        Toggle::make('is_footercompilant')
                            ->label('Footer Conforme GDPR')
                            ->default(false)
                            ->helperText('True se il footer è conforme GDPR'),
                    ]),
            ]);
    }
}
