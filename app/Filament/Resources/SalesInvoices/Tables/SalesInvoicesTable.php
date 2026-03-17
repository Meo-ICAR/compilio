<?php

namespace App\Filament\Resources\SalesInvoices\Tables;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Principal;
use App\Models\SalesInvoice;
use App\Services\SalesInvoiceImportService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SalesInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Numero')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->label('Partita IVA')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_date')
                    ->label('Data Registrazione')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : null),
                TextColumn::make('amount_including_vat')
                    ->label('Importo Totale')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('residual_amount')
                    ->label('Importo Residuo')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($record) => $record->residual_amount > 0 ? 'warning' : 'success'),
                TextColumn::make('document_type')
                    ->label('Tipo Doc.')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('cancelled')
                    ->label('Annullata')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('document_type')
                    ->label('Tipo Documento')
                    ->options(function () {
                        return SalesInvoice::distinct('document_type')
                            ->whereNotNull('document_type')
                            ->pluck('document_type', 'document_type')
                            ->toArray();
                    }),
                Filter::make('registration_date')
                    ->label('Data Registrazione')
                    ->form([
                        DatePicker::make('registered_from')
                            ->label('Da'),
                        DatePicker::make('registered_until')
                            ->label('A'),
                    ])
                    ->query(function (array $data) {
                        return SalesInvoice::query()
                            ->when(
                                $data['registered_from'],
                                fn($query, $date) => $query->whereDate('registration_date', '>=', $date)
                            )
                            ->when(
                                $data['registered_until'],
                                fn($query, $date) => $query->whereDate('registration_date', '<=', $date)
                            );
                    }),
                Filter::make('invoiceable_id')
                    ->label('Non ancora collegato a Cliente / Mandante')
                    ->query(fn($query) => $query->whereNull('invoiceable_id')),
                Filter::make('overdue')
                    ->label('Scadute')
                    ->query(fn($query) => $query->where('due_date', '<', now())->where('residual_amount', '>', 0)),
                Filter::make('open')
                    ->label('Aperte')
                    ->query(fn($query) => $query->where('closed', false)),
                Filter::make('cancelled')
                    ->label('Annullate')
                    ->query(fn($query) => $query->where('cancelled', true)),
            ])
            ->recordActions([
                // ViewAction::make(),
                // EditAction::make(),
                Action::make('attach_to_model')
                    ->label('Aggiungi')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->visible(fn($record) => is_null($record->client_id))
                    ->form([
                        Select::make('client_type')
                            ->label('Tipo')
                            ->options([
                                'App\Models\Client' => 'Clienti',
                                'App\Models\Principal' => 'Mandanti',
                                // 'App\Models\Agent' => 'Agenti',
                            ])
                            ->default('App\Models\Client')
                            ->required()
                            ->reactive(),
                        TextInput::make('client_name')
                            ->label('Nome Record (cerca o crea nuovo)')
                            ->default(fn($record) => $record->customer_name)
                            ->required()
                            ->helperText('Inserisci un nome esistente o uno nuovo per creare automaticamente il record'),
                    ])
                    ->action(function (array $data, $record) {
                        $clientId = null;
                        $searchTerm = $data['client_name'] ?? null;

                        // Se è vuoto o null, crea il record
                        if (is_null($searchTerm) || $searchTerm === '') {
                            $newRecord = match ($data['client_type']) {
                                'App\Models\Client' => Client::create(['name' => $record->customer_name . date('Y-m-d H:i'),
                                    'vat_number' => $record->vat_number]),
                                'App\Models\Principal' => Principal::create(['name' => $record->customer_name . date('Y-m-d H:i'),
                                    'vat_number' => $record->vat_number]),
                                'App\Models\Agent' => Agent::create(['name' => $record->customer_name . date('Y-m-d H:i'),
                                    'vat_number' => $record->vat_number]),

                                default => null
                            };

                            if ($newRecord) {
                                $clientId = $newRecord->id;
                            }
                        } else {
                            // Verifica se esiste un record con questo nome
                            $existingRecord = match ($data['client_type']) {
                                'App\Models\Client' => Client::where('name', $searchTerm)->first(),
                                'App\Models\Agent' => Agent::where('name', $searchTerm)->first(),
                                'App\Models\Principal' => Principal::where('name', $searchTerm)->first(),
                                default => null
                            };

                            if ($existingRecord) {
                                $clientId = $existingRecord->id;
                            } else {
                                // Crea nuovo record con il nome cercato
                                $newRecord = match ($data['client_type']) {
                                    'App\Models\Client' => Client::create(['name' => $searchTerm]),
                                    'App\Models\Agent' => Agent::create(['name' => $searchTerm]),
                                    'App\Models\Principal' => Principal::create(['name' => $searchTerm]),
                                    default => null
                                };

                                if ($newRecord) {
                                    $clientId = $newRecord->id;
                                }
                            }
                        }

                        if ($clientId) {
                            // Prima aggiorna il record corrente
                            $record->update([
                                'invoiceable_type' => $data['client_type'],
                                'invoiceable_id' => $clientId,
                            ]);

                            // Poi associa tutte le altre fatture dello stesso cliente
                            $updatedCount = SalesInvoice::where('customer_name', $record->customer_name)
                                ->whereNull('invoiceable_id')
                                ->update([
                                    'invoiceable_type' => $data['client_type'],
                                    'invoiceable_id' => $clientId,
                                ]);

                            $totalUpdated = $updatedCount + 1;  // +1 per il record corrente

                            $actionText = (is_null($searchTerm) || $searchTerm === '') ? 'creato e associato' : 'associato';
                            Notification::make()
                                ->title('Fatture associate')
                                ->body("{$totalUpdated} fatture del cliente '{$record->customer_name}' {$actionText} correttamente")
                                ->success()
                                ->send();
                        }
                    }),
            ], position: RecordActionsPosition::BeforeColumns)
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_closed')
                        ->label('Chiudi Selezionati')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $count = $records->where('closed', false)->count();
                            $records->where('closed', false)->each->update(['closed' => true]);

                            Notification::make()
                                ->title('Fatture chiuse')
                                ->body("{$count} fatture chiuse correttamente")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Action::make('bulk_attach_to_model')
                        ->label('Associa Cliente Selezionato')
                        ->icon('heroicon-o-link')
                        ->color('success')
                        ->accessSelectedRecords()
                        ->form([
                            Select::make('client_type')
                                ->label('Tipo')
                                ->options([
                                    'App\Models\Client' => 'Clienti',
                                    //  'App\Models\Agent' => 'Agenti',
                                    'App\Models\Principal' => 'Mandanti',
                                ])
                                ->default('App\Models\Principal')
                                ->required()
                                ->reactive(),
                            Select::make('client_id')
                                ->label('Seleziona Record')
                                ->options(function (callable $get) {
                                    $type = $get('client_type');
                                    if (!$type)
                                        return [];

                                    return match ($type) {
                                        'App\Models\Client' => Client::pluck('name', 'id'),
                                        'App\Models\Agent' => Agent::pluck('name', 'id'),
                                        'App\Models\Principal' => Principal::pluck('name', 'id'),
                                        default => []
                                    };
                                })
                                ->required()
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search, callable $get) {
                                    $type = $get('client_type');
                                    if (!$type)
                                        return [];

                                    return match ($type) {
                                        'App\Models\Client' => Client::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'),
                                        'App\Models\Agent' => Agent::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'),
                                        'App\Models\Principal' => Principal::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'),
                                        default => []
                                    };
                                }),
                        ])
                        ->action(function (array $data, $records) {
                            $totalUpdated = 0;
                            $clientId = null;

                            // Se è selezionato "new", crea il record
                            if ($data['client_id'] === 'new') {
                                $newRecord = match ($data['client_type']) {
                                    'App\Models\Client' => Client::create(['name' => $record->customer_name,
                                        'vat_number' => $record->vat_number]),
                                    'App\Models\Principal' => Principal::create(['name' => $record->customer_name,
                                        'vat_number' => $record->vat_number]),
                                    'App\Models\Agent' => Agent::create(['name' => $record->customer_name,
                                        'vat_number' => $record->vat_number]),
                                    default => null
                                };

                                if ($newRecord) {
                                    $clientId = $newRecord->id;
                                }
                            } else {
                                $clientId = $data['client_id'];
                            }

                            if ($clientId) {
                                foreach ($records as $record) {
                                    if (is_null($record->invoiceable_id)) {
                                        // Aggiorna il record corrente
                                        $record->update([
                                            'invoiceable_type' => $data['client_type'],
                                            'invoiceable_id' => $clientId,
                                        ]);
                                        $totalUpdated++;

                                        // Associa tutte le altre fatture dello stesso cliente
                                        $additionalUpdated = SalesInvoice::where('customer_name', $record->customer_name)
                                            ->whereNull('invoiceable_id')
                                            ->where('id', '!=', $record->id)  // Escludi il record corrente
                                            ->update([
                                                'invoiceable_type' => $data['client_type'],
                                                'invoiceable_id' => $clientId,
                                            ]);
                                        $totalUpdated += $additionalUpdated;
                                    }
                                }

                                $actionText = $data['client_id'] === 'new' ? 'creati e associati' : 'associati';
                                Notification::make()
                                    ->title('Fatture associate')
                                    ->body("{$totalUpdated} fatture {$actionText} correttamente (incluse tutte quelle degli stessi clienti)")
                                    ->success()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Action::make('import_sales_invoices')
                    ->label('Importa Fatture Vendita')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('success')
                    ->form([
                        FileUpload::make('import_file')
                            ->label('File CSV/Excel')
                            ->helperText('Carica un file CSV o Excel con i dati delle fatture di vendita')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(10240)  // 10MB
                            ->directory('sales-invoice-imports')
                            ->visibility('private')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = storage_path('app/public/' . $data['import_file']);
                            $companyId = Auth::user()->company_id;
                            $filename = basename($data['import_file']);

                            $importService = new SalesInvoiceImportService($filename);
                            $results = $importService->import($filePath, $companyId);

                            Notification::make()
                                ->title('Importazione completata')
                                ->body("Importazione da {$filename} completata. Importate: {$results['imported']}, Aggiornate: {$results['updated']}, Errori: {$results['errors']}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore importazione')
                                ->body('Errore durante importazione: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('associate_sales_invoices')
                    ->label('Abbina')
                    ->icon('heroicon-o-link')
                    ->color('warning')
                    ->action(function () {
                        try {
                            $companyId = Auth::user()->company_id;
                            $importService = new SalesInvoiceImportService();
                            $importService->setCompanyId($companyId);  // Usa il metodo setter

                            // Esegui solo le funzioni di matching per sales invoices
                            $importService->matchPrincipalsByVatNumber();
                            $importService->matchClientsByVatNumber();

                            Notification::make()
                                ->title('Associazione completata')
                                ->body('Le fatture di vendita sono state associate a mandanti e clienti')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore associazione')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->emptyStateActions([
                //  CreateAction::make(),
            ])
            ->defaultSort('registration_date', 'desc');
    }
}
