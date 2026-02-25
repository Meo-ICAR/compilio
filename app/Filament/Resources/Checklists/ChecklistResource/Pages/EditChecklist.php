<?php

namespace App\Filament\Resources\Checklists\ChecklistResource\Pages;

use App\Filament\Resources\Checklists\ChecklistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChecklist extends EditRecord
{
    protected static string $resource = ChecklistResource::class;
}
