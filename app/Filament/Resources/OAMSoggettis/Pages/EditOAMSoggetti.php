<?php

namespace App\Filament\Resources\OAMSoggettis\Pages;

use App\Filament\Resources\OAMSoggettis\OAMSoggettiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOAMSoggetti extends EditRecord
{
    protected static string $resource = OAMSoggettiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
