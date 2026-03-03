<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PracticeOamSummaryResource\Pages;
use App\Models\PracticeOam;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use BackedEnum;

class PracticeOamSummaryResource extends Resource
{
    protected static ?string $model = PracticeOam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Practice OAM Summary';

    protected static ?string $modelLabel = 'Practice OAM Summary';

    protected static ?string $pluralModelLabel = 'Practice OAM Summaries';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder => PracticeOam::query()
                    ->select([
                        'company_id',
                        'practice_id',
                        'oam_code_id',
                        DB::raw('SUM(compenso) as total_compenso'),
                        DB::raw('SUM(compenso_lavorazione) as total_compenso_lavorazione'),
                        DB::raw('SUM(compenso_premio) as total_compenso_premio'),
                        DB::raw('SUM(compenso_rimborso) as total_compenso_rimborso'),
                        DB::raw('SUM(compenso_assicurazione) as total_compenso_assicurazione'),
                        DB::raw('SUM(compenso_cliente) as total_compenso_cliente'),
                        DB::raw('SUM(storno) as total_storno'),
                        DB::raw('SUM(provvigione) as total_provvigione'),
                        DB::raw('SUM(provvigione_lavorazione) as total_provvigione_lavorazione'),
                        DB::raw('SUM(provvigione_premio) as total_provvigione_premio'),
                        DB::raw('SUM(provvigione_rimborso) as total_provvigione_rimborso'),
                        DB::raw('SUM(provvigione_assicurazione) as total_provvigione_assicurazione'),
                        DB::raw('SUM(provvigione_storno) as total_provvigione_storno'),
                        DB::raw('COUNT(*) as record_count'),
                    ])
                    ->groupBy(['company_id', 'practice_id', 'oam_code_id'])
            )
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('practice.id')
                    ->label('Practice ID')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('oamCode.code')
                    ->label('OAM Code')
                    ->sortable(),
                TextColumn::make('total_compenso')
                    ->label('Total Compenso')
                    ->money('EUR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('EUR'),
                    ]),
                TextColumn::make('total_provvigione')
                    ->label('Total Provvigione')
                    ->money('EUR')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('EUR'),
                    ]),
                TextColumn::make('record_count')
                    ->label('Records')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->modalContent(function ($record) {
                        $details = PracticeOam::where('company_id', $record->company_id)
                            ->where('practice_id', $record->practice_id)
                            ->where('oam_code_id', $record->oam_code_id)
                            ->get();

                        return view('filament.resources.practice-oam-summary-resource.details-modal', [
                            'summary' => $record,
                            'details' => $details,
                        ]);
                    })
                    ->modalWidth('7xl')
                    ->modalHeading(function ($record) {
                        return "Details - Company: {$record->company?->name}, Practice: {$record->practice_id}, OAM: {$record->oamCode?->code}";
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPracticeOamSummaries::route('/'),
        ];
    }
}
