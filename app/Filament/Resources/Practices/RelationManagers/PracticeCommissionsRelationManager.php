<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use App\Filament\Resources\PracticeCommissions\Schemas\PracticeCommissionForm;
use App\Models\PracticeCommission;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PracticeCommissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'practiceCommissions';

    protected static ?string $title = 'Commissioni Pratica';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('practice_commissions.company_id', auth()->user()->company_id))
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('agent.name')
                    ->label('Denominazione')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->label('Perfezionata')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_coordination')
                    ->label('Coord.')
                    ->boolean(),
                TextColumn::make('practiceCommissionStatus.name')
                    ->label('Stato Pagamento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Causale')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Note')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('CRM_code')
                    ->label('CRM')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'PENDING' => 'In Attesa',
                        'APPROVED' => 'Approvata',
                        'REJECTED' => 'Rifiutata',
                    ]),
            ])
            ->headerActions([
                //  CreateAction::make()->label('Nuova Commissione'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifica')
                    ->form(fn($record) => PracticeCommissionForm::configure(new Schema)),
            ])
            ->bulkActions([]);
    }
}
