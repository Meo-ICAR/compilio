<?php

namespace App\Filament\Resources\Practices\Tables;

use App\Models\Practice;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;  // ← Import corretto
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class PracticeOAMsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Provvigione::query()
                ->where('is_OAM_last_year', true)
            //  ->whereNot('annullato', 1))
            )
            ->groups([
                Group::make('practiceScopeOAM')
                    ->label('Tipo Pratica')
                    ->collapsible(),  // SOSTITUISCE le vecchie impostazioni di groupingSettings
            ])
            ->columns([
                TextColumn::make('practiceScopeOAM')
                    ->label('OAM Scope')
                    ->getStateUsing(fn(Practice $record): string => $record->practiceScopeOAM() ?? 'N/A')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('isPerfectedLastYear')
                    ->label('Perfected Last Year')
                    ->getStateUsing(fn(Practice $record): bool => $record->isPerfectedLastYear())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('amount')
                    ->label('Perfezionato')
                    ->searchable()
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                IconColumn::make('isWorkingLastYear')
                    ->label('Working Last Year')
                    ->getStateUsing(fn(Practice $record): bool => $record->isWorkingLastYear())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('amount')
                    ->label('Lavorazione')
                    ->searchable()
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label(''))
                    ->sortable(),
                TextColumn::make('CRM_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Practice Name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('isRejectedLastYear')
                    ->label('Rejected Last Year')
                    ->getStateUsing(fn(Practice $record): bool => $record->isRejectedLastYear())
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                TextColumn::make('inserted_at')
                    ->label('Inserted At')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->label('Perfected At')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->default('N/A'),
                TextColumn::make('rejected_at')
                    ->label('Rejected At')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->default('N/A'),
            ])
            ->filters([
                SelectFilter::make('oam_status')
                    ->label('OAM Status')
                    ->options([
                        'working' => 'Working Last Year',
                        'rejected' => 'Rejected Last Year',
                        'perfected' => 'Perfected Last Year',
                    ])
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
