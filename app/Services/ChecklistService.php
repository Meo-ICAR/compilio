<?php

namespace App\Services;

use App\Models\Checklist;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;

class ChecklistService
{
    /**
     * Genera lo schema Filament dinamico partendo da un template Checklist
     */
    public static function getFormSchema(Checklist $checklist): array
    {
        $schema = [];

        // Recuperiamo le domande ordinate
        $items = $checklist->items()->orderBy('ordine')->get();

        foreach ($items as $item) {
            // Chiavi univoche per il form state di Filament
            $boolKey = "item_{$item->item_code}_bool";
            $textKey = "item_{$item->item_code}_text";
            $filesKey = "item_{$item->item_code}_files";

            // Costruiamo il wrapper della singola domanda
            $fieldGroup = Section::make($item->name)
                ->description($item->question . ($item->description ? ' - ' . $item->description : ''))
                ->schema(function () use ($item, $boolKey, $textKey, $filesKey) {
                    $fields = [];

                    // SE NON CI SONO ALLEGATI (n_documents == 0) -> È una domanda testuale o Vero/Falso
                    if ($item->n_documents == 0) {
                        $fields[] = Grid::make(2)->schema([
                            Toggle::make($boolKey)
                                ->label('Risposta (Sì/No)')
                                ->inline(false)
                                ->live(),  // Reattivo per la logica condizionale
                            Textarea::make($textKey)
                                ->label('Testo della risposta / Note aggiuntive')
                                ->rows(2)
                                ->live(onBlur: true),
                        ]);
                    }
                    // SE CI SONO ALLEGATI (n_documents > 0)
                    else {
                        $fields[] = FileUpload::make($filesKey)
                            ->label('Carica Allegati')
                            ->directory("checklist_files/{$item->attach_model}/{$item->item_code}")
                            ->multiple($item->n_documents > 1)  // Se 99, è multiplo
                            ->maxFiles($item->n_documents > 1 ? null : 1)
                            ->preserveFilenames()
                            ->reorderable()
                            ->appendFiles()
                            ->required($item->is_required);

                        // Anche con i file, lasciamo un campo note opzionale
                        $fields[] = Textarea::make($textKey)
                            ->label('Annotazioni (Opzionale)')
                            ->rows(1);
                    }

                    return $fields;
                })
                ->collapsible()
                ->compact();  // Design più pulito

            // --- APPLICAZIONE LOGICA CONDIZIONALE ---
            if ($item->depends_on_code && $item->dependency_type) {
                // La dipendenza si basa quasi sempre sulla risposta "Vero/Falso" (il toggle)
                $parentBoolKey = "item_{$item->depends_on_code}_bool";

                $fieldGroup->visible(function (Get $get) use ($item, $parentBoolKey) {
                    $parentValue = $get($parentBoolKey);

                    // Normalizza il valore atteso (1 = true, 0 = false)
                    $expectedValue = in_array($item->depends_on_value, ['1', 'true', 'si', 'vero']) ? true : false;

                    if ($item->dependency_type === 'show_if') {
                        return $parentValue === $expectedValue;
                    }

                    if ($item->dependency_type === 'hide_if') {
                        return $parentValue !== $expectedValue;
                    }

                    return true;
                });
            }

            $schema[] = $fieldGroup;
        }

        return $schema;
    }
}
