<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyWebsites\Schemas\CompanyWebsiteForm;
use App\Filament\Resources\CompanyWebsites\Tables\CompanyWebsitesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class WebsitesRelationManager extends RelationManager
{
    protected static string $relationship = 'websites';

    public function form(Schema $schema): Schema
    {
        return CompanyWebsiteForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return CompanyWebsitesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
