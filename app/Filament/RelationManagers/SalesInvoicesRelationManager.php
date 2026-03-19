<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\SalesInvoices\SalesInvoiceResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
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
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SalesInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'salesInvoices';

    protected static ?string $relatedResource = salesInvoiceResource::class;

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
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),
                IconColumn::make('closed')
                    ->label('Closed')
                    ->boolean(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$state || $record->closed)
                            return null;
                        return $state->isPast() ? 'danger' : null;
                    }),
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
                //   DeleteAction::make(),
            ])
            ->defaultSort('customer_name', 'desc');
    }
}
