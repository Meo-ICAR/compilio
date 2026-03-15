<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Models\PurchaseInvoice;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PurchaseInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseInvoices';

    protected static ?string $relatedResource = purchaseInvoiceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
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
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('amount_including_vat')
                    ->label('Amount incl. VAT')
                    ->money('EUR')
                    ->sortable(),
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
                    ->boolean(),
                TextColumn::make('supplier_category')
                    ->label('Category')
                    ->searchable()
                    ->toggleable(),
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
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'App\Models\Client' => 'success',
                        'App\Models\Agent' => 'warning',
                        'App\Models\Principal' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('closed')
                    ->options([
                        '1' => 'Closed',
                        '0' => 'Open',
                    ]),
                Filter::make('overdue')
                    ->label('Overdue')
                    ->query(function ($query) {
                        return $query
                            ->where('closed', false)
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                    }),
                SelectFilter::make('supplier_category')
                    ->label('Supplier Category')
                    ->options(function () {
                        return \App\Models\PurchaseInvoice::whereNotNull('supplier_category')
                            ->distinct()
                            ->pluck('supplier_category', 'supplier_category')
                            ->toArray();
                    }),
                SelectFilter::make('invoiceable_type')
                    ->label('Attached To')
                    ->options([
                        'App\Models\Client' => 'Client',
                        'App\Models\Agent' => 'Agent',
                        'App\Models\Principal' => 'Principal',
                    ]),
            ])
            ->headerActions([
                // CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                // DeleteAction::make(),
            ])
            ->defaultSort('document_date', 'desc');
    }
}
