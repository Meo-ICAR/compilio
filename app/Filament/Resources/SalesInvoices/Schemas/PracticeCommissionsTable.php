<?php

namespace App\Filament\Resources\SalesInvoices\Schemas;

use App\Models\PracticeCommission;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PracticeCommissionsTable
{
    public static function make(string $relationship): Section
    {
        return Section::make('Practice Commission Collegate')
            ->description('Practice commission dove il VAT number del principal corrisponde a quello della fattura di vendita')
            ->schema([
                Placeholder::make('practice_commissions_info')
                    ->label('Practice Commission')
                    ->content(function ($record) {
                        $commissions = $record->practiceCommissions;
                        if ($commissions->isEmpty()) {
                            return 'Nessuna practice commission collegata trovata.';
                        }

                        $content = "Trovate {$commissions->count()} practice commission:\n\n";
                        foreach ($commissions->take(5) as $commission) {
                            $content .= "- {$commission->name} (" . ($commission->principal->name ?? 'N/A') . ") - €{$commission->amount}\n";
                        }

                        if ($commissions->count() > 5) {
                            $content .= '... e altre ' . ($commissions->count() - 5) . ' commission';
                        }

                        return $content;
                    })
            ])
            ->collapsible();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('CRM_code')
                    ->label('Codice CRM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal.name')
                    ->label('Principal')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR')),
                TextColumn::make('practiceCommissionStatus.name')
                    ->label('Stato')
                    ->badge()
                    ->color(fn($record) => $record->practiceCommissionStatus?->color ?? 'gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inserted_at')
                    ->label('Data Inserimento')
                    ->date()
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->label('Data Perfezionamento')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_enasarco')
                    ->label('Enasarco')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_insurance')
                    ->label('Assicurazione')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->label('Numero Fattura')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_at')
                    ->label('Data Fattura')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('inserted_at', 'desc');
    }
}
