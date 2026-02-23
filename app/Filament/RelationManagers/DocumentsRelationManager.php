<?php

namespace App\Filament\RelationManagers;

use App\Models\DocumentType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documenti';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                /*
                 * TextColumn::make('name')
                 *     ->label('Nome File')
                 *     ->searchable()
                 *     ->sortable(),
                 * TextColumn::make('mime_type')
                 *     ->label('Tipo')
                 *     ->badge()
                 *     ->formatStateUsing(fn(string $state): string => $this->getMimeTypeLabel($state)),
                 * TextColumn::make('size')
                 *     ->label('Dimensione')
                 *     ->formatStateUsing(fn(int $state): string => $this->formatBytes($state)),
                 * TextColumn::make('created_at')
                 *     ->label('Caricato il')
                 *     ->dateTime('d/m/Y H:i')
                 *     ->sortable(),
                 */
                TextColumn::make('document_type_id')
                    ->label('Tipo Documento')
                    ->formatStateUsing(fn($state) => $state ? \App\Models\DocumentType::find($state)?->name : '-')
                    ->badge(),
                TextColumn::make('expires_at')
                    ->label('Scadenza')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('name'),
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
                                    ->options(\App\Models\DocumentType::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set) => $set('document_type_preview', DocumentType::find($state)))
                            ]),
                        Step::make('Description')
                            ->description('Compila informazioni documento')
                            ->schema([
                                TextInput::make('name')
                                    //    ->default($documentType?->name)
                                    ->label('Nome Documento'),
                                DatePicker::make('emitted_at')
                                    ->label('Data Emissione')
                                    ->default(now()),
                                DatePicker::make('expires_at')
                                    ->label('Data Scadenza'),
                                TextInput::make('emitted_by')
                                    // ->default($documentType->emitted_by)
                                    ->label('Ente Rilascio'),
                                TextInput::make('docnumber')
                                    ->label('Numero Documento'),
                                SpatieMediaLibraryFileUpload::make('document')
                                    ->label('File')
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
                Select::make('document_type_id')
                    ->label('Tipo Documento')
                    ->options(\App\Models\DocumentType::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('expires_at')
                    ->label('Scade il'),
                TextInput::make('name'),
                SpatieMediaLibraryFileUpload::make('document')
                    ->label('File')
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
