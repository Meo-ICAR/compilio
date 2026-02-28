<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\ClientMandates\ClientMandateResource;
use App\Models\ClientMandate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class ClientMandatesRelationManager extends RelationManager
{
    protected static string $relationship = 'clientMandates';

    protected static ?string $title = 'Mandati Cliente';

    protected static ?string $modelLabel = 'Mandato Cliente';

    protected static ?string $pluralModelLabel = 'Mandati Cliente';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('numero_mandato')
                    ->label('Numero Mandato')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_mandato')
            ->columns([
                Tables\Columns\TextColumn::make('numero_mandato')
                    ->label('Numero Mandato')
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
                    ->searchable()
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('stato')
                    ->label('Stato')
                    ->colors([
                        'attivo' => 'success',
                        'concluso_con_successo' => 'primary',
                        'scaduto' => 'warning',
                        'revocato' => 'danger',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'attivo' => 'Attivo',
                        'concluso_con_successo' => 'Concluso con Successo',
                        'scaduto' => 'Scaduto',
                        'revocato' => 'Revocato',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\Filter::make('data_firma_mandato')
                    ->label('Data Firma')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dal')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('Al')
                            ->native(false),
                    ])
                    ->query(function (Tables\Filter $query, array $data): Tables\Filter {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Tables\Filter $query, $date): Tables\Filter => $query->whereDate('data_firma_mandato', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Tables\Filter $query, $date): Tables\Filter => $query->whereDate('data_firma_mandato', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
