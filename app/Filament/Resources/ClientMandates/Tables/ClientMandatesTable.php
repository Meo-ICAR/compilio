<?php

namespace App\Filament\Resources\ClientMandates\Tables;

use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Filament\Traits\CanExportTable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ClientMandatesTable
{
    use CanExportTable;
    use HasChecklistAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->searchable(),
                TextColumn::make('numero_mandato')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('data_firma_mandato')
                    ->date()
                    ->sortable(),
                TextColumn::make('data_scadenza_mandato')
                    ->date()
                    ->sortable(),
                TextColumn::make('importo_richiesto_mandato')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('scopo_finanziamento')
                    ->label('Scopo Finanziamento')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('purpose_of_relationship')
                    ->label('Scopo Rapporto')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('funds_origin')
                    ->label('Origine Fondi')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('data_consegna_trasparenza')
                    ->label('Consegna Trasparenza')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('oam_delivered')
                    ->label('OAM Consegnato')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role_risk_level')
                    ->label('Rischio Ruolo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'basso' => 'success',
                        'medio' => 'warning',
                        'alto' => 'danger',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'attivo' => 'success',
                        'concluso_con_successo' => 'primary',
                        'scaduto' => 'warning',
                        'revocato' => 'danger',
                    }),
                TextColumn::make('notes')
                    ->label('Note')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
