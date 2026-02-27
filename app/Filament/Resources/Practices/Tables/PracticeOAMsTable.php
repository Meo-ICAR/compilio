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
use Illuminate\Database\Query\Builder as Builderq;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class PracticeOAMsTable
{
    public static function configure(Table $table): Table
    {
        $lastYear = now()->subYear()->endOfYear();
        $startYear = now()->subYear()->startOfYear();
        return $table
            ->query(function () use ($lastYear, $startYear) {
                return Practice::query()
                    ->with(['practiceScope', 'practiceScope.oamScope', 'practiceStatus'])
                    ->where('inserted_at', '<', $lastYear)
                    ->where(function ($query) use ($lastYear, $startYear) {
                        // Condizione 1: Non rifiutata E inserita dopo inizio anno
                        $query
                            ->whereNull('rejected_at')
                            ->where('inserted_at', '>', $startYear);

                        // OR Condizione 2: Rifiutata nell'anno precedente
                        $query->orWhere(function ($query) use ($lastYear, $startYear) {
                            $query
                                ->whereNotNull('rejected_at')
                                ->where('rejected_at', '<', $lastYear)
                                ->where('rejected_at', '>', $startYear);
                        });

                        // OR Condizione 3: Non perfezionata E inserita dopo inizio anno
                        $query->orWhere(function ($query) use ($startYear) {
                            $query
                                ->whereNull('perfected_at')
                                ->where('inserted_at', '>', $startYear);
                        });

                        // OR Condizione 4: Perfezionata nell'anno precedente
                        $query->orWhere(function ($query) use ($lastYear, $startYear) {
                            $query
                                ->whereNotNull('perfected_at')
                                ->where('perfected_at', '<', $lastYear)
                                ->where('perfected_at', '>', $startYear);
                        });
                    })
                    ->whereHas('practiceScope', function ($query) {
                        $query
                            ->whereNotNull('oam_code')
                            ->where('oam_code', '!=', '');
                    })
                    ->whereHas('practiceStatus', function ($query) {
                        $query
                            ->where('is_working', true)
                            ->orWhere('is_perfectioned', true);
                    })
                    ->limit(15);
            })
            /*
             * ->groups([
             *     Group::make('practiceScope.name')
             *         ->label('OAM Scope')
             *         ->titlePrefixedWithLabel(false)
             *         ->collapsible()
             *         ->getTitleFromRecordUsing(function (Practice $record): string {
             *             $scopeName = $record->practiceScope?->name ?? 'N/A';
             *             $count = Practice::whereHas('practiceScope', function ($query) use ($record) {
             *                 $query->where('name', $record->practiceScope?->name);
             *             })->count();
             *             return "{$scopeName} ({$count})";
             *         }),
             * ])
             * //  ->collapsedGroupsByDefault()
             * ->groupsOnly()
             */
            ->columns([
                TextColumn::make('practiceScope.name')
                    ->label('OAM Scope')
                    //  ->getStateUsing(fn(Practice $record): string => $record->practiceScope?->oamScope?->name ?? 'N/A')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('In Lavorazione')
                    ->searchable()
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label('')->query(fn(Builderq $query) => $query->whereNull('perfected_at')))
                    ->sortable(),
                TextColumn::make('inserted_at')
                    ->label('Inserted At')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Perfezionato')
                    ->searchable()
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->summarize(Sum::make()->money('EUR')->label('')->query(fn(Builderq $query) => $query->whereNotNull('perfected_at')))
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->label('Perfezionato Il')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CRM_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Practice Name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    //    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
