<?php

namespace App\Filament\Resources\Agents\Tables;

use App\Filament\Imports\AgentsImporter;
use App\Filament\Traits\CanExportTable;
use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\Agent;
use App\Models\Rui;
use App\Services\AgentImportService;
use App\Services\ChecklistService;
use App\Services\GeminiVisionService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;

class AgentsTable
{
    // 2. Usa il Trait nella classe della Risorsa
    use HasChecklistAction;
    use CanExportTable;

    public static function configure(Table $table): Table
    {
        return $table
            ->selectable()
            ->paginated(['all', 10, 25, 50, 100])
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Agente')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->label('CF / Partita IVA')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('oam_at')
                    ->label('OAM dal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stipulated_at')
                    ->label('Collaboratore dal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dismissed_at')
                    ->label('Dimesso il')
                    ->date()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('numero_iscrizione_rui')
                    ->label('Numero OAM')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable(),
                TextColumn::make('coordinatedBy.name')
                    ->label('Coordinato da (Dip.)')
                    ->sortable()
                    ->placeholder('Nessuno'),
                TextColumn::make('coordinatedByAgent.name')
                    ->label('Coordinato da (Agente)')
                    ->sortable()
                    ->placeholder('Nessuno'),
                TextColumn::make('supervisor_type')
                    ->label('Tipo Supervisore')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'no' => 'gray',
                        'si' => 'green',
                        'filiale' => 'blue',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'no' => 'No',
                        'si' => 'Sì',
                        'filiale' => 'Filiale',
                        default => $state,
                    }),
                TextColumn::make('ivass')
                    ->label('Codice IVASS')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable(),
                TextColumn::make('ivass_section')
                    ->label('Sezione IVASS')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'A' => 'blue',
                        'B' => 'green',
                        'C' => 'yellow',
                        'D' => 'orange',
                        'E' => 'purple',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('stipulated_at')
                    ->label('Stipula')
                    ->date()
                    ->sortable(),
                TextColumn::make('dismissed_at')
                    ->label('Cessazione')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable(),
                TextColumn::make('contribute')
                    ->label('Contributo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remburse')
                    ->label('Rimborso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('vat_name')
                    ->label('Ragione Sociale')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                IconColumn::make('is_art108')
                    ->label('Esente art. 108')
                    ->boolean()
                    ->trueIcon('heroicon-s-shield-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->color(fn($state) => $state ? 'success' : 'gray'),
                TextColumn::make('updated_at')
                    ->label('Aggiornato')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->default(true)
                    ->placeholder('Tutti')
                    ->trueLabel('Attivi')
                    ->falseLabel('Non Attivi'),
                TernaryFilter::make('numero_iscrizione_rui')
                    ->label('Iscritto OAM')
                    ->placeholder('Tutti')
                    ->trueLabel('Iscritto OAM')
                    ->falseLabel('Non Iscritto OAM')
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(!empty($data['numero_iscrizione_rui']), function ($query) use ($data) {
                                return $query->whereNotNull('numero_iscrizione_rui');
                            });
                    }),
                Filter::make('anomalie_oam')
                    ->label('Anomalie OAM')
                    ->query(function ($query) {
                        // Confronta i valori delle due colonne
                        return $query->whereColumn('oam_at', '>', 'stipulated_at');
                    }),
                Filter::make('dismissed_at')
                    ->label('Eventuale cessazione successiva al')
                    ->form([
                        DatePicker::make('dismissed_at_to')
                            ->label('Risulta ancora attivo alla data del ')
                            ->placeholder('Ignora dimissioni successive a tale data'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dismissed_at_to'], function ($query) use ($data) {
                                $query
                                    ->where('stipulated_at', '<', $data['dismissed_at_to'])
                                    ->where(function ($query) use ($data) {
                                        $query
                                            ->where('dismissed_at', '>=', $data['dismissed_at_to'])
                                            ->orWhereNull('dismissed_at');
                                    });
                            });
                    }),
            ])
            ->recordActions([
                ...self::getChecklistActions(
                    code: 'AUDIT_RETE_AGENTI',  // <-- Il 'code' esatto presente nel tuo DB
                    label: 'Audit',
                    // icon: 'heroicon-o-clipboard-document-check'
                ),
            ], position: RecordActionsPosition::BeforeColumns)
            ->headerActions([
                Action::make('import_agents_excel')
                    ->label('Importa Agenti Excel')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('success')
                    ->form([
                        FileUpload::make('import_file_excel')
                            ->label('File Excel')
                            ->helperText('Carica un file Excel con i dati degli agenti')
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(10240)  // 10MB
                            ->directory('agent-imports')
                            ->visibility('private')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = storage_path('app/private/' . $data['import_file_excel']);
                            $companyId = Auth::user()->company_id;
                            $filename = basename($data['import_file_excel']);

                            $importService = new AgentImportService();
                            $results = $importService->importAgents($filePath, $companyId);

                            Notification::make()
                                ->title('Importazione Agenti completata')
                                ->body("Importazione da {$filename} completata. Importati: {$results['imported']}, Saltati: {$results['skipped']}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore importazione Agenti')
                                ->body('Errore durante importazione: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('match_agents_rui')
                    ->label('Abbina Agenti a OAM')
                    ->icon('heroicon-o-link')
                    ->color('warning')
                    ->action(function () {
                        try {
                            $companyId = Auth::user()->company_id;
                            $matchedCount = 0;
                            $errors = [];

                            // Get all agents
                            $agents = Agent::where('company_id', $companyId)->get();

                            foreach ($agents as $agent) {
                                // Try to find matching RUI record by name
                                $rui = Rui::where('cognome_nome', 'like', '%' . $agent->name . '%')
                                    ->first();

                                if ($rui && !$agent->numero_iscrizione_rui) {
                                    // Update agent with RUI registration number
                                    $agent->update([
                                        'numero_iscrizione_rui' => $rui->numero_iscrizione_rui,
                                        'oam_at' => $rui->data_iscrizione
                                    ]);
                                    $matchedCount++;
                                }
                            }

                            Notification::make()
                                ->title('Abbinamento Agenti a OAM completata')
                                ->body("Abbinate trovate: {$matchedCount}, Errori: " . count($errors))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore abbina Agenti a OAM')
                                ->body('Errore durante abbina: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    static::getExportBulkAction(),  // 2. Richiama l'azione dal trait
                    //   DeleteBulkAction::make(),
                ]),
            ])
            ->defaultsort('name');
    }
}
