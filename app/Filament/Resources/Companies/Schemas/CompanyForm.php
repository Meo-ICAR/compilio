<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('vat_number'),
                TextInput::make('vat_name'),
                TextInput::make('oam'),
                DatePicker::make('oam_at'),
                TextInput::make('oam_name'),
                Select::make('company_type_id')
                    ->relationship('companyType', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->label('Logo Azienda')
                    ->image()
                    ->imageEditor()
                    ->directory('companies/logos')
                    ->visibility('public')
                    ->collection('logo')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml', 'image/webp'])
                    ->helperText("Carica il logo dell'azienda (max 2MB, formati: JPG, PNG, SVG, WebP)"),
                RichEditor::make('page_header')
                    ->label('Intestazione Carta Intestata')
                    ->helperText("Testo che apparirà nell'intestazione dei documenti ufficiali")
                    ->columnSpanFull(),
                RichEditor::make('page_footer')
                    ->label('Piè di Pagina Carta Intestata')
                    ->helperText('Testo che apparirà nel piè di pagina dei documenti ufficiali')
                    ->columnSpanFull(),
            ]);
    }
}
