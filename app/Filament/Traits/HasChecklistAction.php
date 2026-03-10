<?php

namespace App\Filament\Traits;

use App\Models\Checklist;
use Filament\Actions\Action;  // Assicurati che sia questo per le tabelle
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

trait HasChecklistAction
{
    public static function getChecklistActions(
        ?string $code = null,
        string $label = 'Checklist',
        string $icon = 'heroicon-o-clipboard-document-check'
    ): array {
        $codeName = $code ?? 'generale';

        // 1. AZIONE GENERA
        $actionGenera = Action::make("genera_checklist_{$codeName}")
            ->label("Genera {$label}")
            ->icon('heroicon-o-sparkles')
            ->color('success')
            ->hidden(fn($record) => $record->checklists()->where('code', $code)->exists())
            ->requiresConfirmation()
            ->action(function ($record) use ($code, $label) {
                $template = Checklist::where('code', $code)->whereNull('checklistable_id')->first();

                if (!$template) {
                    Notification::make()->title('Template non trovato')->danger()->send();
                    return;
                }

                $nuova = $record->checklists()->create([
                    'code' => $code,
                    'name' => $template->name ?? $label,
                ]);

                foreach ($template->items as $item) {
                    $nuova->items()->create([
                        'name' => $item->name,
                        'description' => $item->description,
                        'question' => $item->question,
                        'url_step' => $item->url_step,
                        'document_type' => $item->document_type,
                        'is_completed' => false,
                    ]);
                }
                Notification::make()->title('Generata!')->success()->send();
            });

        // 2. AZIONE GESTISCI
        $actionGestisci = Action::make("gestisci_checklist_{$codeName}")
            ->label($label)
            ->icon($icon)
            ->color('warning')
            ->slideOver()
            ->visible(fn($record) => $record->checklists()->where('code', $code)->exists())
            ->fillForm(function ($record) {
                dd($record->checklists()->toSql());  // Se arrivi qui, scrivi questo per vedere la query
                return [];
            })
            /*
             * ->fillForm(function ($record) use ($code): array {
             *     $checklist = $record->checklists()->where('code', $code)->first();
             *     if (!$checklist)
             *         return [];
             *
             *     // Carichiamo i dati in modo esplicito
             *     return [
             *         'testata_nome' => $checklist->name,
             *         'items' => $checklist->items->map(fn($item) => [
             *             'id' => $item->id,
             *             'question' => $item->question,
             *             'description' => $item->description,
             *             'url_step' => $item->url_step,
             *             'answer' => $item->answer,
             *             'document_type' => $item->document_type,
             *         ])->toArray(),
             *     ];
             *
             * })
             */
            ->form([
                Section::make('Dettagli')
                    ->schema([
                        Placeholder::make('testata_nome')->label('Checklist')->content(fn($get) => $get('testata_nome')),
                    ]),
                Repeater::make('items')
                    ->dehydrated(false)  // FONDAMENTALE: Evita il loop di stato di Filament
                    ->schema([
                        TextInput::make('id')->hidden(),
                        TextInput::make('url_step')->hidden(),
                        TextInput::make('document_type')->hidden(),
                        TextInput::make('question')->label('Domanda')->disabled()->columnSpan(3),
                        Placeholder::make('link')
                            ->label('Link')
                            ->content(fn($get) => $get('url_step')
                                ? new HtmlString("<a href='{$get('url_step')}' target='_blank' style='color:blue;text-decoration:underline'>Apri link</a>")
                                : '-')
                            ->columnSpan(1),
                        Textarea::make('description')->label('Istruzioni')->disabled()->rows(2)->columnSpan(4),
                        Textarea::make('answer')->label('Risposta')->rows(3)->columnSpan(4),
                        SpatieMediaLibraryFileUpload::make('file_upload')
                            ->label('Allegato')
                            ->collection(fn($get) => $get('document_type') ?? 'default')
                            ->visible(fn($get) => filled($get('document_type')))
                            ->columnSpan(4),
                    ])
                    ->columns(4)
                    ->addable(false)
                    ->deletable(false)
            ])
            ->action(function ($record, array $data) use ($code) {
                $checklist = $record->checklists()->where('code', $code)->first();

                // Nota: poiché abbiamo usato dehydrated(false), dobbiamo recuperare
                // i dati del repeater direttamente dall'input grezzo della richiesta
                $rawItems = request()->input('components.0.calls.0.params.0.data.items')
                    ?? data_get($data, 'items', []);

                foreach ($rawItems as $itemData) {
                    $item = $checklist->items()->find($itemData['id']);
                    if ($item) {
                        $item->update([
                            'answer' => $itemData['answer'] ?? null,
                            'is_completed' => filled($itemData['answer'])
                        ]);

                        // Gestione file (Spatie se presente nel form)
                        // Il componente Spatie gestisce il caricamento sul record ($record)
                        // se configurato correttamente nel form.
                    }
                }
                Notification::make()->title('Salvato')->success()->send();
            });

        return [$actionGenera, $actionGestisci];
    }
}
