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
            ->filters([])
            ->headerActions([
                //  CreateAction::make()->label('Nuova Commissione'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifica')
                    ->form(fn($record) => PracticeCommissionForm::configure(new Schema)),
            ])
            ->recordActions([
                // EditAction::make(),
                Action::make('annulla')
                    ->label('Annulla storno')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible($record->is_storno && ($record->tipo === 'Istituto'))
                    //  ->icon('heroicon-o-erase')
                    ->action(function (array $data, PracticeCommission $record): void {
                        $record->update([
                            'is_storno' => false,
                            'storned_at' => null,
                            'storno_amount' => null,
                        ]);

                        PracticeCommission::where('practice_id', $record->practice_id)
                            ->where('is_storno', '=', true)
                            ->whereNull('invoice_number')
                            ->delete();
                        Notification::make()
                            ->title('Storno Rimosso')
                            ->success()
                            ->send();
                    }),
                Action::make('storna')
                    ->label('Storna')
                    ->visible(fn($record): bool => ($record->tipo === 'Istituto') && !$record->is_storno && !empty($record->invoice_at))
                    ->form([
                        TextInput::make('quota')
                            ->label('Importo Storno')
                            ->numeric()
                            ->required()
                            ->maxValue(fn($record) => $record->amount)
                            ->prefix('€')
                    ])
                    ->action(function (array $data, PracticeCommission $record): void {
                        $provvigioneattiva = $record->amount;
                        $quotaPercent = -$data['quota'] / $provvigioneattiva;

                        // Update the current record
                        $record->update([
                            'storno_amount' => $data['quota'],
                            'storned_at' => now(),
                        ]);

                        // Get all related 'Uscita' provvigioni for the same pratica that are not 'Annullato'
                        $relatedUscite = PracticeCommission::where('practice_id', $record->practice_id)
                            ->where('is_storno', '!=', true)
                            ->get();

                        // Update each related 'Uscita' record
                        foreach ($relatedUscite as $uscita) {
                            $newRecord = $uscita->replicate();
                            //  $newRecord->id = $record->id . '-';
                            // 2. Modifica eventuali campi (es. aggiungi "Copia" al titolo)
                            $newRecord->is_storno = true;
                            $newRecord->storned_at = now();
                            $newRecord->amount = $uscita->amount * $quotaPercent;
                            $newRecord->name = 'Storno provvigione ' . $record->CRM_code;

                            // 3. Salva il nuovo record nel database
                            $newRecord->save();
                        }
                        Notification::make()
                            ->title('Storno Effettuato')
                            ->success()
                            ->send();
                    })
                    ->iconButton()
                    ->color('primary'),
            ])
            ->bulkActions([]);
    }
}
