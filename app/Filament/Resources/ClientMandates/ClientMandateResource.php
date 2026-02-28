<?php

namespace App\Filament\Resources\ClientMandates;

use App\Filament\Resources\ClientMandates\Pages\ListClientMandates;
use App\Filament\Resources\ClientMandates\RelationManagers\PracticesRelationManager;
use App\Models\ClientMandate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientMandateResource extends Resource
{
    protected static ?string $model = ClientMandate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestione Clienti';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_mandato')
                    ->label('Numero Mandato')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('data_firma_mandato')
                    ->label('Data Firma Mandato')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('data_scadenza_mandato')
                    ->label('Data Scadenza Mandato')
                    ->native(false),

                Forms\Components\TextInput::make('importo_richiesto_mandato')
                    ->label('Importo Richiesto')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),

                Forms\Components\TextInput::make('scopo_finanziamento')
                    ->label('Scopo Finanziamento')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('data_consegna_trasparenza')
                    ->label('Data Consegna Trasparenza')
                    ->native(false),

                Forms\Components\Select::make('stato')
                    ->label('Stato')
                    ->options([
                        'attivo' => 'Attivo',
                        'concluso_con_successo' => 'Concluso con Successo',
                        'scaduto' => 'Scaduto',
                        'revocato' => 'Revocato',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_mandato')
                    ->label('Numero Mandato')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_firma_mandato')
                    ->label('Data Firma')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('data_scadenza_mandato')
                    ->label('Data Scadenza')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('importo_richiesto_mandato')
                    ->label('Importo Richiesto')
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('scopo_finanziamento')
                    ->label('Scopo Finanziamento')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('stato')
                    ->label('Stato')
                    ->colors([
                        'attivo' => 'success',
                        'concluso_con_successo' => 'primary',
                        'scaduto' => 'warning',
                        'revocato' => 'danger',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'attivo' => 'Attivo',
                        'concluso_con_successo' => 'Concluso con Successo',
                        'scaduto' => 'Scaduto',
                        'revocato' => 'Revocato',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stato')
                    ->label('Stato')
                    ->options([
                        'attivo' => 'Attivo',
                        'concluso_con_successo' => 'Concluso con Successo',
                        'scaduto' => 'Scaduto',
                        'revocato' => 'Revocato',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PracticesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientMandates::route('/'),
            'create' => Pages\CreateClientMandate::route('/create'),
            'edit' => Pages\EditClientMandate::route('/{record}/edit'),
        ];
    }
}
