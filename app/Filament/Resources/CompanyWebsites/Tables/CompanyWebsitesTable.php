<?php

namespace App\Filament\Resources\CompanyWebsites\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyWebsitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('principal'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Sito')
                    ->searchable(),
                TextColumn::make('domain')
                    ->label('Dominio')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipologia')
                    ->searchable(),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->placeholder('Nessuno'),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                TextColumn::make('url_privacy')
                    ->label('Privacy URL')
                    //  ->url()
                    ->placeholder('Non impostato')
                    ->toggleable(),
                TextColumn::make('url_cookies')
                    ->label('Cookies URL')
                    //  ->url()
                    ->placeholder('Non impostato')
                    ->toggleable(),
                IconColumn::make('is_footercompilant')
                    ->label('GDPR Footer')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
