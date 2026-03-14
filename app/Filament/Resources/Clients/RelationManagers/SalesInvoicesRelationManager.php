<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\Resources\SalesInvoices\SalesInvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class SalesInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'salesInvoices';

    protected static ?string $relatedResource = SalesInvoiceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('closed')
                    ->label('Closed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$state || $record->closed)
                            return null;
                        return $state->isPast() ? 'danger' : null;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('closed')
                    ->options([
                        '1' => 'Closed',
                        '0' => 'Open',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(function ($query) {
                        return $query
                            ->where('closed', false)
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
