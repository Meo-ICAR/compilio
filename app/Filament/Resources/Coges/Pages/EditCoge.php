<?php

namespace App\Filament\Resources\Coges\Pages;

use App\Filament\Resources\Coges\CogeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoge extends EditRecord
{
    protected static string $resource = CogeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
