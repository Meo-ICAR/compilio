<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyFunctions\Schemas\CompanyFunctionForm;
use App\Filament\Resources\CompanyFunctions\Tables\CompanyFunctionsTable;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class CompanyFunctionsRelationManager extends RelationManager
{
    protected static string $relationship = 'companyFunctions';

    protected static ?string $modelLabel = 'Funzione Aziendale';

    protected static ?string $pluralModelLabel = 'Funzioni Aziendali';

    protected static ?string $title = 'Funzioni Aziendali';

    public function form(Schema $schema): Schema
    {
        return CompanyFunctionForm::configure($schema);
    }

    public function title(string $title): string
    {
        return 'Funzionogramma';
    }

    public function table(Table $table): Table
    {
        return CompanyFunctionsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('replicate')
                        ->label('Clona Funzioni')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('warning')
                        ->action(function (array $records) {
                            $replicatedCount = 0;
                            $errors = [];

                            foreach ($records as $record) {
                                try {
                                    $newFunction = $record->replicate();
                                    $newFunction->name = $record->name . ' (copia)';
                                    $newFunction->save();
                                    $replicatedCount++;
                                } catch (\Exception $e) {
                                    $errors[] = 'Errore nel duplicare "' . $record->name . '": ' . $e->getMessage();
                                }
                            }

                            $message = $replicatedCount . ' funzioni duplicate con successo';
                            if (!empty($errors)) {
                                $message .= '. Errori: ' . implode(', ', $errors);
                            }

                            Notification::make()
                                ->title('Duplicazione Completata')
                                ->body($message)
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Conferma Duplicazione')
                        ->modalDescription('Sei sicuro di voler duplicare le funzioni selezionate?'),
                ]),
            ]);
    }
}
