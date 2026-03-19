<?php

namespace App\Filament\Resources\Checklists\Pages;

use App\Filament\Resources\Checklists\ChecklistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChecklist extends EditRecord
{
    protected static string $resource = ChecklistResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Eager load checklist items to prevent N+1 queries and timeouts
        $this->record->load(['checklistItems' => function ($query) {
            $query->orderBy('ordine');
        }]);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
