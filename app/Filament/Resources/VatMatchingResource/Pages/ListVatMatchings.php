<?php

namespace App\Filament\Resources\VatMatchingResource\Pages;

use App\Filament\Resources\VatMatchingResource;
use Filament\Resources\Pages\ListRecords;

class ListVatMatchings extends ListRecords
{
    protected static string $resource = VatMatchingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Azione per eseguire il matching VAT
            \Filament\Actions\Action::make('run-vat-matching')
                ->label('Esegui Matching VAT')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    \Artisan::call('commissions:match-principals-by-vat');
                    \Filament\Notifications\Notification::make()
                        ->title('Matching VAT completato')
                        ->success()
                        ->send();
                }),
        ];
    }
}
