<?php

namespace App\Filament\RelationManagers;

use App\Models\Website;
use App\Services\TransparencyScanService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class WebsitesRelationManager extends RelationManager
{
    protected static string $relationship = 'websites';
    protected static ?string $title = 'Siti Web';

    /*
     * public function (Schema $schema): Schema
     * {
     *     return $schema
     *         ->components([
     *             TextInput::make('name')
     *                 ->label('Nome Sito')
     *                 ->required()
     *                 ->maxLength(255),
     *             TextInput::make('url')
     *                 ->label('URL Sito')
     *                 ->required()
     *                 ->url()
     *                 ->maxLength(255),
     *             TextInput::make('url_transparency')
     *                 ->label('URL Trasparenza')
     *                 ->url()
     *                 ->maxLength(255)
     *                 ->helperText('URL della pagina trasparenza se diversa dal sito principale'),
     *             Textarea::make('description')
     *                 ->label('Descrizione')
     *                 ->maxLength(500)
     *                 ->columnSpanFull(),
     *             Select::make('type')
     *                 ->label('Tipo Sito')
     *                 ->options([
     *                     'corporate' => 'Sito Corporate',
     *                     'institutional' => 'Sito Istituzionale',
     *                     'portal' => 'Portale',
     *                     'ecommerce' => 'E-commerce',
     *                     'blog' => 'Blog',
     *                     'other' => 'Altro',
     *                 ])
     *                 ->default('corporate'),
     *             Toggle::make('is_active')
     *                 ->label('Attivo')
     *                 ->default(true),
     *             Toggle::make('requires_transparency')
     *                 ->label('Richiede Trasparenza')
     *                 ->default(false)
     *                 ->helperText('Se true, il sito verrà scansionato per documenti di trasparenza'),
     *         ]);
     * }
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Sito')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->copyMessage('URL copiato!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('url_transparency')
                    ->label('URL Trasparenza')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('Non impostato')
                    ->copyable()
                    ->copyMessage('URL copiato!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'corporate' => 'primary',
                        'institutional' => 'success',
                        'portal' => 'info',
                        'ecommerce' => 'warning',
                        'blog' => 'secondary',
                        'other' => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('requires_transparency')
                    ->label('Richiede Trasparenza')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo Sito')
                    ->options([
                        'corporate' => 'Sito Corporate',
                        'institutional' => 'Sito Istituzionale',
                        'portal' => 'Portale',
                        'ecommerce' => 'E-commerce',
                        'blog' => 'Blog',
                        'other' => 'Altro',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Attivo'),
                Tables\Filters\TernaryFilter::make('requires_transparency')
                    ->label('Richiede Trasparenza'),
                Tables\Filters\Filter::make('has_transparency_url')
                    ->label('Ha URL Trasparenza')
                    ->query(fn($query) => $query->whereNotNull('url_transparency')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('run_transparency_scan')
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
                            // Get company ID based on owner type
                            $companyId = $this->getCompanyIdFromOwner($ownerRecord);

                            if (!$companyId) {
                                Notification::make()
                                    ->title('Errore Scansione')
                                    ->body("Impossibile determinare l'azienda associata")
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $results = $scanService->scanForCompany($companyId);

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
                        return $ownerRecord && $this->getWebsitesWithTransparency($ownerRecord)->isNotEmpty();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('test_url')
                    ->label('Test URL')
                    ->icon('heroicon-o-globe-alt')
                    ->color('info')
                    ->url(fn(Website $record): string => $record->url)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Get company ID from various owner types
     */
    private function getCompanyIdFromOwner($ownerRecord): ?string
    {
        // Handle different owner types
        if (method_exists($ownerRecord, 'company_id')) {
            return $ownerRecord->company_id;
        }

        if (method_exists($ownerRecord, 'id') && get_class($ownerRecord) === 'App\Models\Company') {
            return $ownerRecord->id;
        }

        // Add more owner type handling as needed
        return null;
    }

    /**
     * Get websites that have transparency URL
     */
    private function getWebsitesWithTransparency($ownerRecord)
    {
        $websites = $ownerRecord->websites();

        if (method_exists($websites, 'whereNotNull')) {
            return $websites->whereNotNull('url_transparency')->get();
        }

        return collect();
    }
}
