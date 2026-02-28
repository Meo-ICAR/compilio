<?php

namespace App\Filament\Resources\CompanyWebsites\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyWebsiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nome Sito'),
                TextInput::make('domain')
                    ->required()
                    ->label('Dominio')
                    ->helperText('es. agenzia-x.mediaconsulence.it'),
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
                Toggle::make('is_active')
                    ->label('Sito Attivo')
                    ->default(true),
                TextInput::make('url_privacy')
                    ->label('URL Privacy Policy')
                    //  ->url()
                    ->nullable()
                    ->helperText('Link alla pagina privacy policy'),
                TextInput::make('url_cookies')
                    ->label('URL Cookie Policy')
                    //  ->url()
                    ->nullable()
                    ->helperText('Link alla pagina cookie policy'),
                Toggle::make('is_footercompilant')
                    ->label('Footer Conforme GDPR')
                    ->default(false)
                    ->helperText('True se il footer è conforme GDPR'),
            ]);
    }
}
