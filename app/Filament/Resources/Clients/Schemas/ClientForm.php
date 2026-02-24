<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                Toggle::make('privacy_consent')
                    ->label('Consenso Privacy')
                    ->helperText('Indica se il cliente ha dato il consenso al trattamento dei dati personali')
                    ->default(false),
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
