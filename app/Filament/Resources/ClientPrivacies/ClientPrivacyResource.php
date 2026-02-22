<?php

namespace App\Filament\Resources\ClientPrivacies;

use App\Filament\Resources\ClientPrivacies\Pages\ManageClientPrivacies;
use App\Models\ClientPrivacy;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ClientPrivacyResource extends Resource
{
    protected static ?string $model = ClientPrivacy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Privacy';

    protected static ?string $recordTitleAttribute = 'request_type';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->required(),
                Select::make('request_type')
                    ->label('Tipo Richiesta')
                    ->options([
                        'Accesso' => 'Accesso',
                        'Rettifica' => 'Rettifica',
                        'Cancellazione' => 'Cancellazione',
                        'Portabilità' => 'Portabilità',
                    ])
                    ->required(),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'Ricevuta' => 'Ricevuta',
                        'In lavorazione' => 'In lavorazione',
                        'Evasa' => 'Evasa',
                    ])
                    ->required(),
                DateTimePicker::make('completed_at')
                    ->label('Data della risposta definitiva'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('request_type')
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('request_type')
                    ->label('Tipo Richiesta')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ricevuta' => 'info',
                        'In lavorazione' => 'warning',
                        'Evasa' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('completed_at')
                    ->label('Data Evasione')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data Richiesta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClientPrivacies::route('/'),
        ];
    }
}
