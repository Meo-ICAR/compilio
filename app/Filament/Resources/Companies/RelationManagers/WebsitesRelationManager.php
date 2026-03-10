<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyWebsites\Schemas\CompanyWebsiteForm;
use App\Filament\Resources\CompanyWebsites\Tables\CompanyWebsitesTable;
use App\Services\TransparencyScanService;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class WebsitesRelationManager extends RelationManager
{
    protected static string $relationship = 'websites';
    protected static ?string $title = 'Siti Web';

    public function form(Schema $schema): Schema
    {
        return CompanyWebsiteForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return CompanyWebsitesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
                Actions\Action::make('run_transparency_scan')
                    ->label('Scansione Trasparenza')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Scansione Trasparenza')
                    ->modalDescription('Esegui la scansione dei siti web per trovare documenti di trasparenza')
                    ->modalSubmitActionLabel('Avvia Scansione')
                    ->action(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        $scanService = new TransparencyScanService();

                        try {
                            $results = $scanService->scanForCompany($ownerRecord->id);

                            $message = "Scansione completata!\n"
                                . "Siti processati: {$results['processed_websites']}\n"
                                . "Pagine trasparenza trovate: {$results['found_transparency_pages']}\n"
                                . "Documenti estratti: {$results['extracted_documents']}";

                            Notification::make()
                                ->title('Scansione Trasparenza Completata')
                                ->body($message)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore Scansione')
                                ->body('Si è verificato un errore durante la scansione: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        return $ownerRecord && $ownerRecord->websites()->whereNotNull('url_transparency')->exists();
                    }),
            ]);
    }
}
