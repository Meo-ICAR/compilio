<?php

namespace App\Filament\Resources\Processes\Pages;

use App\Filament\Resources\Processes\ProcessResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcesses extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = ProcessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
