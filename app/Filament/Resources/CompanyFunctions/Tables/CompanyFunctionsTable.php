<?php

namespace App\Filament\Resources\CompanyFunctions\Tables;

use App\Filament\Traits\CanExportTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyFunctionsTable
{
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('businessFunction.name')
                    ->label('Funzione')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('employee.name')
                    ->label('Referente Interno')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assegnato'),
                TextColumn::make('client.name')
                    ->label('Referente Esterno')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assegnato'),
                TextColumn::make('report_frequency')
                    ->label('Frequenza Report')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non specificata'),
            ])
            ->filters([
                SelectFilter::make('business_function_id')
                    ->label('Funzione')
                    ->relationship('businessFunction', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('is_privacy')
                    ->label('Privacy')
                    ->options([
                        '1' => 'Sì',
                        '0' => 'No',
                    ]),
                SelectFilter::make('is_outsourced')
                    ->label('Stato')
                    ->options([
                        '0' => 'Interno',
                        '1' => 'Esternalizzato',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
