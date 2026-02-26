<?php

namespace App\Filament\Resources\PrincipalEmployees\Pages;

use App\Filament\Resources\PrincipalEmployees\PrincipalEmployeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalEmployees extends ListRecords
{
    protected static string $resource = PrincipalEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
