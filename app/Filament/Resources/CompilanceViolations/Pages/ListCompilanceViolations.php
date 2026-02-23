<?php

namespace App\Filament\Resources\CompilanceViolations\Pages;

use App\Filament\Resources\CompilanceViolations\CompilanceViolationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompilanceViolations extends ListRecords
{
    protected static string $resource = CompilanceViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
