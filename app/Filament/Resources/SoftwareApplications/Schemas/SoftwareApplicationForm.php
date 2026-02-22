<?php

namespace App\Filament\Resources\SoftwareApplications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SoftwareApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('provider_name'),
                TextInput::make('website_url')
                    ->url(),
                TextInput::make('api_url')
                    ->url(),
                TextInput::make('sandbox_url')
                    ->url(),
                TextInput::make('api_key_url')
                    ->url(),
                Textarea::make('api_parameters')
                    ->columnSpanFull(),
                Toggle::make('is_cloud'),
            ]);
    }
}
