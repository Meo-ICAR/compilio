<?php

namespace App\Filament\Resources\AuiRecords;

use App\Filament\Resources\AuiRecords\Pages\ListAuiRecords;
use App\Models\AuiRecord;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class AuiRecordResource extends Resource
{
    protected static ?string $model = AuiRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Compliance AUI';

    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Record AUI';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codice_univoco_aui')
                    ->label('Codice Univoco AUI')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\Select::make('practice_id')
                    ->label('Pratica')
                    ->relationship('practice')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('tipo_registrazione')
                    ->label('Tipo Registrazione')
                    ->options([
                        'instaurazione' => 'Instaurazione Rapporto',
                        'esecuzione_operazione' => 'Esecuzione Operazione',
                        'chiusura_rapporto' => 'Chiusura Rapporto',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('data_registrazione')
                    ->label('Data Registrazione')
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('importo_operazione')
                    ->label('Importo Operazione')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                Forms\Components\Select::make('profilo_rischio')
                    ->label('Profilo Rischio')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('motivo_annullamento')
                    ->label('Motivo Annullamento')
                    ->rows(3)
                    ->visible(fn(callable $get) => $get('is_annullato')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codice_univoco_aui')
                    ->label('Codice AUI')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Codice AUI copiato negli appunti'),
                Tables\Columns\TextColumn::make('practice.name')
                    ->label('Pratica')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tipo_registrazione')
                    ->label('Tipo')
                    ->colors([
                        'instaurazione' => 'info',
                        'esecuzione_operazione' => 'success',
                        'chiusura_rapporto' => 'warning',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'instaurazione' => 'Instaurazione',
                        'esecuzione_operazione' => 'Esecuzione',
                        'chiusura_rapporto' => 'Chiusura',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('data_registrazione')
                    ->label('Data Registrazione')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('importo_operazione')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('EUR')
                            ->label('Totale')
                    ),
                Tables\Columns\BadgeColumn::make('profilo_rischio')
                    ->label('Rischio')
                    ->colors([
                        'basso' => 'success',
                        'medio' => 'warning',
                        'alto' => 'danger',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                        default => ucfirst($state),
                    }),
                Tables\Columns\IconColumn::make('is_annullato')
                    ->label('Annullato')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('motivo_annullamento')
                    ->label('Motivo Annullamento')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_registrazione')
                    ->label('Tipo Registrazione')
                    ->options([
                        'instaurazione' => 'Instaurazione Rapporto',
                        'esecuzione_operazione' => 'Esecuzione Operazione',
                        'chiusura_rapporto' => 'Chiusura Rapporto',
                    ]),
                Tables\Filters\SelectFilter::make('profilo_rischio')
                    ->label('Profilo Rischio')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ]),
                Tables\Filters\Filter::make('is_annullato')
                    ->label('Solo Annullati')
                    ->query(fn(Tables\Filters\Filter $query, array $data): Tables\Filters\Filter =>
                        $query->where('is_annullato', $data['value'])),
                Tables\Filters\Filter::make('importo_min')
                    ->label('Importo Minimo')
                    //  ->form(fn (): Forms\Components\TextInput::make('value')->label('Importo')->numeric()->prefix('€'))
                    ->query(fn(Tables\Filters\Filter $query, array $data): Tables\Filters\Filter =>
                        $query->where('importo_operazione', '>=', $data['value'] ?? 0)),
                Tables\Filters\Filter::make('importo_max')
                    ->label('Importo Massimo')
                    //    ->form(fn (): Forms\Components\TextInput::make('value')->label('Importo')->numeric()->prefix('€'))
                    ->query(fn(Tables\Filters\Filter $query, array $data): Tables\Filters\Filter =>
                        $query->where('importo_operazione', '<=', $data['value'] ?? 999999)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Annulla Record AUI')
                    ->modalDescription('Sei sicuro di voler annullare questo record AUI? Questa azione è irreversibile.'),
                //   ->modalSubmitActionLabel('Sì, Annulla'),
                //   ->modalCancelActionLabel('Annulla'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Annulla Record AUI Selezionati')
                        ->modalDescription('Sei sicuro di voler annullare questi record AUI? Questa azione è irreversibile.'),
                    //       ->modalSubmitActionLabel('Sì, Annulla')
                    //      ->modalCancelActionLabel('Annulla'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuiRecords::route('/'),
        ];
    }
}
