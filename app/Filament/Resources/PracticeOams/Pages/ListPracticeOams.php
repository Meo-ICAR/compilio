<?php

namespace App\Filament\Resources\PracticeOams\Pages;

use App\Filament\Resources\PracticeOams\PracticeOamResource;
use App\Services\PracticeOamService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPracticeOams extends ListRecords
{
    protected static string $resource = PracticeOamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_oam')
                ->label('Sincronizza OAM')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    try {
                        $service = app(PracticeOamService::class);
                        $service->syncPracticeOamsForCompany(null, null, null);

                        Notification::make()
                            ->title('Sincronizzazione completata')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Errore durante la sincronizzazione')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            //  CreateAction::make(),
        ];
    }
}
