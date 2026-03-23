<?php

namespace App\Filament\Resources\Processes\Pages;

use App\Exports\RaciMatrixExport;
use App\Filament\Resources\Processes\ProcessResource;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\ProcessTask;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Maatwebsite\Excel\Facades\Excel;

class EditProcess extends EditRecord
{
    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_raci')
                ->label('Scarica Matrice Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn() => Excel::download(
                    new RaciMatrixExport($this->record->id),
                    "Matrice_RACI_{$this->record->slug}.xlsx"
                )),
            Action::make('openChecklist')
                ->hidden(fn() => !Checklist::where('process_id', $this->record->id)->exists())
                //   ->url(fn() => route('filament.app.resources.checklists.edit', Checklist::where('process_id', $this->record->id))),
                ->label('Apri Checklist')
                ->color('info')
                ->icon('heroicon-o-clipboard-document-check'),
            Action::make('generateChecklist')
                ->hidden(fn() => Checklist::where('process_id', $this->record->id)->exists())
                ->label('Genera Checklist')
                ->color('success')
                ->icon('heroicon-o-clipboard-document-check')
                ->requiresConfirmation()
                ->action(function () {
                    $process = $this->record;

                    // 1. Crea la testata della Checklist
                    $checklist = Checklist::create([
                        'name' => $process->name . ' - Checklist',
                        'process_code' => $process->groupcode,
                        'process_id' => $process->id,
                        // aggiungi altri campi necessari...
                    ]);

                    // 2. Recupera i task definiti per questo processo
                    $tasks = ProcessTask::where('process_id', $this->record->id)->get();

                    foreach ($tasks as $task) {
                        // 3. Crea l'item della checklist per ogni task
                        ChecklistItem::create([
                            'checklist_id' => $checklist->id,
                            //   'process_id' => $process->id,
                            'process_task_id' => $task->id,
                            'name' => $task->name,
                            'is_completed' => false,
                            'business_function_id' => null,  // Da mappare se necessario
                        ]);
                    }

                    Notification::make()
                        ->title('Checklist generata con successo!')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
