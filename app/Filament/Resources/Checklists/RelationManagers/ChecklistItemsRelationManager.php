<?php

namespace App\Filament\Resources\Checklists\RelationManagers;

use App\Models\ChecklistItem;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class ChecklistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklistItems';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Informazioni Elemento')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Elemento')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('question')
                            ->label('Domanda')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Configurazione')
                    ->schema([
                        Forms\Components\Checkbox::make('is_required')
                            ->label('Obbligatorio')
                            ->default(false),
                        Forms\Components\Checkbox::make('is_document_required')
                            ->label('Richiede Documento')
                            ->default(false),
                        Forms\Components\Select::make('attach_model')
                            ->label('Modello Allegato')
                            ->options([
                                'principal' => 'Principal',
                                'agent' => 'Agent',
                                'company' => 'Company',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('attach_model_id')
                            ->label('ID Modello')
                            ->nullable(),
                        Forms\Components\TextInput::make('repeatable_code')
                            ->label('Codice Ripetibile')
                            ->helperText('Es: doc_annuale per documenti annuali')
                            ->nullable(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Logica Condizionale')
                    ->schema([
                        Forms\Components\TextInput::make('item_code')
                            ->label('Codice Univoco')
                            ->helperText('Codice univoco della domanda per dipendenze')
                            ->nullable(),
                        Forms\Components\TextInput::make('depends_on_code')
                            ->label('Dipende da Codice')
                            ->helperText('Il codice della domanda da cui dipende')
                            ->nullable(),
                        Forms\Components\TextInput::make('depends_on_value')
                            ->label('Valore Dipendenza')
                            ->helperText('Il valore che deve avere per attivarsi')
                            ->nullable(),
                        Forms\Components\Select::make('dependency_type')
                            ->label('Tipo Dipendenza')
                            ->options([
                                'show_if' => 'Mostra se',
                                'hide_if' => 'Nascondi se',
                            ])
                            ->nullable(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Risposta e Note')
                    ->schema([
                        Forms\Components\Textarea::make('answer')
                            ->label('Risposta')
                            ->rows(3)
                            ->nullable(),
                        Forms\Components\Textarea::make('annotation')
                            ->label('Annotazioni Interne')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('question')
                    ->label('Domanda')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Obbligatorio')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_document_required')
                    ->label('Richiede Doc')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('attach_model')
                    ->label('Allegato a')
                    ->colors([
                        'primary' => 'principal',
                        'success' => 'agent',
                        'warning' => 'company',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'principal' => 'Principal',
                        'agent' => 'Agent',
                        'company' => 'Company',
                        default => 'Nessuno',
                    }),
                Tables\Columns\TextColumn::make('repeatable_code')
                    ->label('Codice Ripetibile')
                    ->searchable()
                    ->placeholder('No'),
                Tables\Columns\TextColumn::make('item_code')
                    ->label('Codice Univoco')
                    ->searchable()
                    ->placeholder('No'),
                Tables\Columns\BadgeColumn::make('dependency_type')
                    ->label('Tipo Dipendenza')
                    ->colors([
                        'success' => 'show_if',
                        'danger' => 'hide_if',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'show_if' => 'Mostra se',
                        'hide_if' => 'Nascondi se',
                        default => 'Nessuna',
                    }),
                Tables\Columns\TextColumn::make('depends_on_code')
                    ->label('Dipende da')
                    ->searchable()
                    ->placeholder('Nessuna'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Obbligatorio'),
                Tables\Filters\TernaryFilter::make('is_document_required')
                    ->label('Richiede Documento'),
                Tables\Filters\SelectFilter::make('attach_model')
                    ->label('Modello Allegato')
                    ->options([
                        'principal' => 'Principal',
                        'agent' => 'Agent',
                        'company' => 'Company',
                    ]),
                Tables\Filters\SelectFilter::make('dependency_type')
                    ->label('Tipo Dipendenza')
                    ->options([
                        'show_if' => 'Mostra se',
                        'hide_if' => 'Nascondi se',
                    ]),
                Tables\Filters\TernaryFilter::make('has_dependency')
                    ->label('Ha Dipendenze')
                    ->query(fn($query) => $query->whereNotNull('depends_on_code')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
