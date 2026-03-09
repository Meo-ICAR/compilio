<?php

namespace App\Filament\Resources\SosReports\Tables;

use App\Filament\Resources\Checklists\ChecklistResource;
use App\Models\SosReport;
use App\Services\ChecklistService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class SosReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('Non assegnato'),
                TextColumn::make('stato')
                    ->label('Stato')
                    ->badge()
                    ->color(fn($record): string => $record->stato_color)
                    ->formatStateUsing(fn($record): string => $record->stato_label),
                TextColumn::make('grado_sospetto')
                    ->label('Grado Sospetto')
                    ->badge()
                    ->color(fn($record): string => $record->grado_sospetto_color)
                    ->formatStateUsing(fn($record): string => $record->grado_sospetto_label),
                TextColumn::make('motivo_sospetto')
                    ->label('Motivo')
                    //  ->limitWords(10)
                    ->searchable(),
                TextColumn::make('data_segnalazione_uif')
                    ->label('Data Segnalazione UIF')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Non segnalata'),
                TextColumn::make('responsabile.name')
                    ->label('Responsabile')
                    ->searchable()
                    ->placeholder('Non assegnato'),
                IconColumn::make('has_checklist')
                    ->label('Checklist')
                    ->boolean()
                    ->getStateUsing(function (SosReport $record): bool {
                        return $record
                            ->checklist()
                            ->where('code', 'SOS_WORKFLOW')
                            ->exists();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('stato')
                    ->label('Stato')
                    ->options([
                        'istruttoria' => 'Istruttoria',
                        'archiviata' => 'Archiviata',
                        'segnalata_uif' => 'Segnalata UIF',
                    ]),
                SelectFilter::make('grado_sospetto')
                    ->label('Grado Sospetto')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ]),
            ])
            ->recordActions([
                Action::make('apriSOS')
                    ->label('Segnalazione SOS')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('warning')
                    ->action(function (SosReport $record, ChecklistService $checklistService) {
                        try {
                            // Verifichiamo se esiste già una checklist per questo client privacy
                            $existingChecklist = $record
                                ->checklist()
                                ->where('code', 'SOS_WORKFLOW')
                                ->first();

                            if ($existingChecklist) {
                                // Se esiste, mostriamo una notifica e reindirizziamo
                                Notification::make()
                                    ->info()
                                    ->title('Checklist Già Presente')
                                    ->body('La checklist esiste già. Puoi compilarla o modificarla.')
                                    ->send();

                                // Reindirizziamo alla pagina di modifica della checklist
                                return redirect()->to(ChecklistResource::getUrl('edit', ['record' => $existingChecklist]));
                            } else {
                                // Se non esiste, la creiamo
                                $checklistService->assignTemplate($record, 'SOS_WORKFLOW');

                                Notification::make()
                                    ->success()
                                    ->title('Checklist Assegnata!')
                                    ->body('La Checklist è pronta per essere compilata')
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Errore')
                                ->body("Errore durante l'assegnazione della checklist: " . $e->getMessage())
                                ->send();
                        }
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
