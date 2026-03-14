<?php

namespace App\Filament\Resources\SalesInvoices\Tables;

use App\Models\SalesInvoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalesInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Numero')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_date')
                    ->label('Data Registrazione')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : null),
                TextColumn::make('amount_including_vat')
                    ->label('Importo Totale')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('residual_amount')
                    ->label('Importo Residuo')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($record) => $record->residual_amount > 0 ? 'warning' : 'success'),
                TextColumn::make('document_type')
                    ->label('Tipo Doc.')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('closed')
                    ->label('Chiusa')
                    ->boolean(),
                IconColumn::make('cancelled')
                    ->label('Annullata')
                    ->boolean(),
                IconColumn::make('email_sent')
                    ->label('Email')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('document_type')
                    ->label('Tipo Documento')
                    ->options(function () {
                        return \App\Models\SalesInvoice::distinct('document_type')
                            ->whereNotNull('document_type')
                            ->pluck('document_type', 'document_type')
                            ->toArray();
                    }),
                Filter::make('registration_date')
                    ->label('Data Registrazione')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('registered_from')
                            ->label('Da'),
                        \Filament\Forms\Components\DatePicker::make('registered_until')
                            ->label('A'),
                    ])
                    ->query(function (array $data) {
                        return SalesInvoice::query()
                            ->when(
                                $data['registered_from'],
                                fn($query, $date) => $query->whereDate('registration_date', '>=', $date)
                            )
                            ->when(
                                $data['registered_until'],
                                fn($query, $date) => $query->whereDate('registration_date', '<=', $date)
                            );
                    }),
                Filter::make('overdue')
                    ->label('Scadute')
                    ->query(fn($query) => $query->where('due_date', '<', now())->where('residual_amount', '>', 0)),
                Filter::make('open')
                    ->label('Aperte')
                    ->query(fn($query) => $query->where('closed', false)),
                Filter::make('cancelled')
                    ->label('Annullate')
                    ->query(fn($query) => $query->where('cancelled', true)),
            ])
            ->actions([
                // ViewAction::make(),
                //  EditAction::make(),
            ])
            ->bulkActions([
                //   BulkActionGroup::make([
                //   DeleteBulkAction::make(),
                //   ]),
            ])
            ->emptyStateActions([
                //  CreateAction::make(),
            ])
            ->defaultSort('registration_date', 'desc');
    }
}
