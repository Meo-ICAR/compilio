<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Documenti';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('document')
                    ->label('Anteprima')
                    ->collection('documents')
                    ->conversion('thumb')
                    ->defaultImageUrl(fn($record) => $this->getFileIcon($record->mime_type)),
                TextColumn::make('name')
                    ->label('Nome File')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => $this->getMimeTypeLabel($state)),
                TextColumn::make('size')
                    ->label('Dimensione')
                    ->formatStateUsing(fn(int $state): string => $this->formatBytes($state)),
                TextColumn::make('created_at')
                    ->label('Caricato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('document_type_id')
                    ->label('Tipo Documento')
                    ->formatStateUsing(fn($state) => $state ? \App\Models\DocumentType::find($state)?->name : '-')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Carica Documento')
                    ->modalHeading('Carica Nuovo Documento')
                    ->modalSubmitActionLabel('Carica')
            ])
            ->actions([
                EditAction::make()
                    ->label('Rinomina')
                    ->modalHeading('Rinomina Documento'),
                DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ]),
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
