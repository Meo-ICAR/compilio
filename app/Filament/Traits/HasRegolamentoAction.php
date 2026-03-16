<?php

namespace App\Filament\Traits;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait HasRegolamentoAction
{
    /**
     * Aggiunge l'azione Regolamento alla pagina List
     */
    protected function getRegolamentoAction(): Action
    {
        return Action::make('regolamento')
            ->label('Regolamento')
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->url(function () {
                $modelClass = static::getResource()::getModel();
                $modelName = class_basename($modelClass);

                // Verifica se esiste un documento di regolamento specifico per questo modello
                $document = Document::where('documentable_type', $modelClass)
                    ->whereNull('documentable_id')  // Cerca documento template
                    ->first();

                if ($document) {
                    // Se il documento ha un URL, reindirizza lì
                    if ($document->url_document) {
                        return $document->url_document;
                    }

                    // Se il documento ha media, reindirizza al primo media
                    if ($document->hasMedia()) {
                        $media = $document->getFirstMedia();
                        return $media->getUrl();
                    }

                    // Altrimenti reindirizza alla pagina di edit del documento
                    return route('filament.admin.resources.documents.edit', ['record' => $document->id]);
                }

                // Mostra notifica se non trovato

                /*
                 * Notification::make()
                 *     ->title('Regolamento non trovato')
                 *     ->body("Nessun documento di regolamento trovato per {$modelName}")
                 *     ->warning()
                 *     ->send();
                 */
                return null;
            })
            ->openUrlInNewTab()
            ->disabled(function () {
                // Disabilita il pulsante se non ci sono documenti di regolamento
                $modelClass = static::getResource()::getModel();
                $modelName = class_basename($modelClass);

                // Verifica se esiste un documento di regolamento specifico per questo modello
                $document = Document::where('documentable_type', $modelClass)
                    ->whereNull('documentable_id')  // Cerca documento template
                    ->first();

                return $document !== null;
            });
    }

    /**
     * Override del metodo getHeaderActions per includere l'azione Regolamento
     */
    protected function getHeaderActions(): array
    {
        $actions = parent::getHeaderActions();

        // Aggiungi l'azione Regolamento se disponibile
        $regolamentoAction = $this->getRegolamentoAction();
        if ($regolamentoAction) {
            $actions[] = $regolamentoAction;
        }

        return $actions;
    }
}
