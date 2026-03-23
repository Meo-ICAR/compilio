<?php

namespace App\Filament\Resources\GdprControllers\Pages;

use App\Filament\Resources\GdprControllers\GdprControllerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditGdprController extends EditRecord
{
    protected static string $resource = GdprControllerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
