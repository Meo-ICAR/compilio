<?php

namespace App\Filament\Resources\Checklists\ChecklistResource\Pages;

use App\Filament\Resources\Checklists\ChecklistResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChecklist extends CreateRecord
{
    protected static string $resource = ChecklistResource::class;
}
