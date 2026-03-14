<?php

namespace App\Filament\Resources\PurchaseInvoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoiceable_type')
                    ->label('Attached To')
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return 'None';
                        return match ($state) {
                            'App\Models\Client' => 'Client',
                            'App\Models\Agent' => 'Agent',
                            'App\Models\Principal' => 'Principal',
                            default => class_basename($state),
                        };
                    })
                    ->sortable(),
                TextColumn::make('invoiceable.name')
                    ->label('Record Name')
                    ->getStateUsing(function ($record) {
                        if (!$record->invoiceable_type || !$record->invoiceable_id) {
                            return '—';
                        }
                        return $record->invoiceable?->name ?? '—';
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR')),
                TextColumn::make('amount_including_vat')
                    ->label('Amount incl. VAT')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR')),
                TextColumn::make('residual_amount')
                    ->label('Residual')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR'))
                    ->color(function ($state) {
                        return $state > 0 ? 'warning' : 'success';
                    }),
                TextColumn::make('document_date')
                    ->label('Document Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$state || $record->closed)
                            return null;
                        return $state->isPast() ? 'danger' : null;
                    }),
                IconColumn::make('closed')
                    ->label('Closed')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('cancelled')
                    ->label('Cancelled')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('invoiceable_type')
                    ->label('Attached To')
                    ->options([
                        'App\Models\Client' => 'Client',
                        'App\Models\Agent' => 'Agent',
                        'App\Models\Principal' => 'Principal',
                    ]),
                Filter::make('open_invoices')
                    ->label('Open Invoices')
                    ->query(fn($query) => $query->where('closed', false)),
                Filter::make('overdue')
                    ->label('Overdue')
                    ->query(function ($query) {
                        return $query
                            ->where('closed', false)
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                    }),
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('document_date', 'desc');
    }
}
