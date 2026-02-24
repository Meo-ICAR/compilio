<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_person')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('tax_code'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                Toggle::make('is_pep'),
                Toggle::make('is_sanctioned'),
                // All'interno del tuo $form->schema([ ... ])
                Section::make('Consensi Privacy e GDPR (Art. 13 e 14)')
                    ->description("Gestione delle autorizzazioni legali del cliente. L'attivazione registra automaticamente data e ora del consenso.")
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('privacy_policy_version')
                            ->label('Versione Informativa')
                            ->default('v1.0-' . date('Y'))
                            ->readOnly()
                            ->helperText('Traccia quale versione del documento è stata firmata.')
                            ->columnSpanFull(),
                        Toggle::make('privacy_policy_read_at')
                            ->label('Presa Visione Informativa')
                            ->onColor('success')
                            ->formatStateUsing(fn($state) => $state !== null)
                            ->dehydrateStateUsing(fn($state, ?Model $record) => $state ? ($record?->privacy_policy_read_at ?? now()) : null),
                        Toggle::make('general_consent_at')
                            ->label('Consenso Privacy Generale')
                            ->helperText("Obbligatorio per l'inserimento anagrafica e gestione base.")
                            ->onColor('success')
                            ->formatStateUsing(fn($state) => $state !== null)
                            ->dehydrateStateUsing(fn($state, ?Model $record) => $state ? ($record?->general_consent_at ?? now()) : null),
                        Toggle::make('consent_sic_at')
                            ->label('Consenso Interrogazione SIC (CRIF/CTC)')
                            ->helperText("Obbligatorio per l'istruttoria del merito creditizio.")
                            ->onColor('success')
                            ->formatStateUsing(fn($state) => $state !== null)
                            ->dehydrateStateUsing(fn($state, ?Model $record) => $state ? ($record?->consent_sic_at ?? now()) : null),
                        Toggle::make('consent_special_categories_at')
                            ->label('Consenso Dati Particolari (Sanitari)')
                            ->helperText('Necessario solo per Polizze CPI e Cessioni del Quinto.')
                            ->onColor('warning')
                            ->formatStateUsing(fn($state) => $state !== null)
                            ->dehydrateStateUsing(fn($state, ?Model $record) => $state ? ($record?->consent_special_categories_at ?? now()) : null),
                        Toggle::make('consent_marketing_at')
                            ->label('Consenso Marketing Diretto')
                            ->helperText('Opzionale. Per invio email, SMS e contatti commerciali.')
                            ->onColor('info')
                            ->formatStateUsing(fn($state) => $state !== null)
                            ->dehydrateStateUsing(fn($state, ?Model $record) => $state ? ($record?->consent_marketing_at ?? now()) : null),
                        Toggle::make('consent_profiling_at')
                            ->label('Consenso Profilazione')
                            ->helperText('Opzionale. Per analisi abitudini di consumo.')
                            ->onColor('info')
                            ->formatStateUsing(fn($state) => $state !== null)
                            ->dehydrateStateUsing(fn($state, ?Model $record) => $state ? ($record?->consent_profiling_at ?? now()) : null),
                    ]),
                FileUpload::make('photo')
                    ->label('Foto Cliente')
                    ->image()
                    ->imageEditor()
                    ->directory('clients/photos')
                    ->visibility('public')
                    ->collection('photos')
                    ->maxSize(5120)  // 5MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                    ->helperText('Carica una foto del cliente (max 5MB, formati: JPG, PNG, WebP)')
                    ->columnSpanFull(),
                Select::make('client_type_id')
                    ->label('Tipo Cliente')
                    ->options(function () {
                        $client = $this->getRecord();
                        $isCompany = $client?->is_company ?? false;
                        return \App\Models\ClientType::where('is_company', $isCompany)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
            ]);
    }
}
