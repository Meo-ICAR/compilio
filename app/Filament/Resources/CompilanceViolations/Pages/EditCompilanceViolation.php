<?php

namespace App\Filament\Resources\CompilanceViolations\Pages;

use App\Filament\Resources\CompilanceViolations\CompilanceViolationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompilanceViolation extends EditRecord
{
    protected static string $resource = CompilanceViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
