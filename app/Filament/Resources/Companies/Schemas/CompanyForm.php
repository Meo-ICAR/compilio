<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
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
