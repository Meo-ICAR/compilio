<?php

namespace App\Filament\RelationManagers;

use App\Models\Agent;
use App\Models\Client;
use App\Models\DocumentType;
use App\Models\Practice;
use App\Models\Principal;
use App\Traits\HasDocumentTypeFiltering;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    use HasDocumentTypeFiltering;

    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documenti';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Documento')
                    ->sortable()
                    ->searchable()
                    // Rende il testo blu e sottolineato come un link
                    ->color('primary')
                    ->weight('bold')
                    // Genera il link dinamico dall'URL nel database
                    ->url(fn($record): ?string => $record->url_document)
                    // Apre il documento in una nuova scheda del browser
                    ->openUrlInNewTab(),
                TextColumn::make('documentType.name')
                    ->label('Tipo Documento')
                    ->sortable()
                    ->searchable()
                    // Opzionale: anche qui puoi mettere un badge per renderlo più leggibile
                    ->badge(),
                TextColumn::make('emitted_at')
                    ->label('Del')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Scadenza')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([])  // headerFilters
            ->headerActions([
                CreateAction::make()
                    ->steps([
                        Step::make('Name')
                            ->description('Carica documento')
                            ->schema([
                                Select::make('document_type_id')
                                    ->label('Tipo Documento')
                                    ->options(function () {
                                        $ownerRecord = $this->getOwnerRecord();
                                        return $this->getFilteredDocumentTypes($ownerRecord);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $documentType = DocumentType::find($state);
                                        $set('document_type_preview', $documentType);
                                        if ($documentType && empty($get('name'))) {
                                            $set('name', $documentType->name);
                                        }
                                    })
                            ]),
                        Step::make('Description')
                            ->description('Compila informazioni documento')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nome Documento')
                                        ->columnSpan(2),
                                    DatePicker::make('emitted_at')
                                        ->label('Data Emissione')
                                        ->default(now()),
                                    DatePicker::make('expires_at')
                                        ->label('Data Scadenza'),
                                    TextInput::make('emitted_by')
                                        ->label('Ente Rilascio'),
                                    TextInput::make('docnumber')
                                        ->label('Numero Documento'),
                                ]),
                                Section::make('Carica File')
                                    ->description('Carica il documento in formato PDF, immagine o Word')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('document')
                                            ->label('File Documento')
                                            ->collection('documents')
                                            ->disk('public')
                                            ->preserveFilenames()
                                            ->downloadable()
                                            ->previewable(true)
                                            ->imageEditor()
                                            ->maxSize(10240)  // 10MB
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'image/jpeg',
                                                'image/png',
                                                'image/jpg',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                            ])
                                            ->required(),
                                    ])
                            ])
                    ])
            ])  // Action::make('create_document')
            ->actions([
                // AZIONE PER VEDERE IL DOCUMENTO
                Action::make('view_document')
                    ->label('Apri')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn($record) => $record->getFirstMediaUrl('documents'))
                    ->openUrlInNewTab(),  // Apre il PDF o l'immagine in una nuova scheda
                // Azione di Download
                Action::make('download')
                    ->label('Scarica')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn($record) => $record->getFirstMedia('documents')?->toResponse(request())),
                EditAction::make()
                    ->label('Modifica')
                    ->modalHeading('Modifica Documento'),
                DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ])
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Documento')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('document_type_id')
                                ->label('Tipo Documento')
                                ->options(function () {
                                    $ownerRecord = $this->getOwnerRecord();
                                    return $this->getFilteredDocumentTypes($ownerRecord);
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $documentType = DocumentType::find($state);
                                    if ($documentType && empty($get('name'))) {
                                        $set('name', $documentType->name);
                                    }
                                })
                                ->columnSpan(2),
                            DatePicker::make('expires_at')
                                ->label('Scade il'),
                            TextInput::make('name')
                                ->label('Nome Documento'),
                        ]),
                    ]),
                Section::make('File Documento')
                    ->description('Carica il documento in formato PDF, immagine o Word')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('document')
                            ->label('File Documento')
                            ->collection('documents')
                            ->disk('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->previewable(true)
                            ->imageEditor()
                            ->maxSize(10240)  // 10MB
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->required(),
                    ])
            ]);
    }

    private function getFileIcon(string $mimeType): string
    {
        return match (true) {
            str_contains($mimeType, 'pdf') => '/icons/pdf.png',
            str_contains($mimeType, 'word') => '/icons/doc.png',
            str_contains($mimeType, 'image') => '/icons/image.png',
            default => '/icons/file.png',
        };
    }

    private function getMimeTypeLabel(string $mimeType): string
    {
        return match (true) {
            str_contains($mimeType, 'pdf') => 'PDF',
            str_contains($mimeType, 'word') => 'Word',
            str_contains($mimeType, 'jpeg') || str_contains($mimeType, 'jpg') => 'JPEG',
            str_contains($mimeType, 'png') => 'PNG',
            default => 'File',
        };
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
