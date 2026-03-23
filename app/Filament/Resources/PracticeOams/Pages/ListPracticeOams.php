<?php

namespace App\Filament\Resources\PracticeOams\Pages;

use App\Filament\Resources\PracticeOams\PracticeOamResource;
use App\Filament\Traits\HasRegolamentoAction;
use App\Services\PracticeOamService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPracticeOams extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = PracticeOamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_practice')
                ->label('Importa Pratiche')
                ->requiresConfirmation()
                ->modalHeading('Conferma importazione pratiche')
                ->modalDescription('Sei sicuro di voler importare le pratiche? Questa operazione ripristina tutti i dati esistenti annullando le forzature.')
                ->action(function () {
                    try {
                        $service = new PracticeOamService();
                        $service->syncPracticeOamsForCompany(Auth::user()->company_id, null, null);

                        Notification::make()
                            ->title('Importazione completata')
                            ->body('Le pratiche sono state importate con successo.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title("Errore durante l'importazione")
                            ->body('Si è verificato un errore: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
