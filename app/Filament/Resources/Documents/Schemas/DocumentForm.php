<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
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

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->label('Tipo Documento')
                    ->required(),
                TextInput::make('name')
                    ->label('Nome Documento')
                    ->required(),
                Toggle::make('is_template')
                    ->label('Template Fornito')
                    ->default(false)
                    ->helperText('Indica se forniamo noi il documento'),
                TextInput::make('status')
                    ->label('Stato')
                    ->required()
                    ->default('uploaded'),
                DatePicker::make('expires_at')
                    ->label('Data scadenza'),
                DatePicker::make('emitted_at')
                    ->label('Data emissione'),
                TextInput::make('docnumber')
                    ->label('Numero documento'),
                TextInput::make('emitted_by')
                    ->label('Ente rilascio'),
                Toggle::make('is_signed')
                    ->label('Firmato')
                    ->default(false),
            ]);
    }
}
