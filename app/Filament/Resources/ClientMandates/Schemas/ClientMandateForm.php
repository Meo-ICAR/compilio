<?php

namespace App\Filament\Resources\ClientMandates\Schemas;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ClientMandateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('numero_mandato')
                    ->required()
                    ->default(function () {
                        // Genera automaticamente: MAND-CLIENT_ID-ANNO-PROGRESSIVO
                        $year = date('Y');

                        // Trova l'ultimo progressivo per questo anno
                        $lastProgressive = \App\Models\ClientMandate::whereYear('created_at', '=', $year)
                            ->orderBy('numero_mandato', 'desc')
                            ->first();

                        if ($lastProgressive) {
                            // Estrai il numero progressivo (es: MAND-000001-2026-001 -> 001)
                            preg_match('/MAND-\d{6}-\d{4}-(\d+)/', $lastProgressive->numero_mandato, $matches);
                            $progressive = ($matches[1] ?? '001') + 1;
                        } else {
                            $progressive = 1;
                        }

                        return 'MAND-' . str_pad($progressive, 6, '0', STR_PAD_LEFT) . "-{$year}";
                    }),
                TextInput::make('name')
                    ->label('Descrizione')
                    ->placeholder('Descrizione del mandato'),
                DatePicker::make('data_firma_mandato')
                    ->required(),
                DatePicker::make('data_scadenza_mandato')
                    ->required(),
                TextInput::make('importo_richiesto_mandato')
                    ->numeric()
                    ->prefix('€'),
                TextInput::make('scopo_finanziamento')
                    ->label('Scopo Finanziamento'),
                Textarea::make('purpose_of_relationship')
                    ->label('Scopo del Rapporto')
                    ->placeholder('Es: Acquisto prima casa'),
                Textarea::make('funds_origin')
                    ->label('Origine Fondi')
                    ->placeholder('Es: Risparmi, donazione, stipendio'),
                DatePicker::make('data_consegna_trasparenza')
                    ->label('Data Consegna Trasparenza'),
                Toggle::make('oam_delivered')
                    ->label('Foglio Informativo Consegnato')
                    ->default(false),
                Select::make('role_risk_level')
                    ->label('Livello Rischio Ruolo')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ])
                    ->default('medio'),
                Select::make('stato')
                    ->options([
                        'attivo' => 'Attivo',
                        'concluso_con_successo' => 'Concluso con successo',
                        'scaduto' => 'Scaduto',
                        'revocato' => 'Revocato',
                    ])
                    ->default('attivo')
                    ->required(),
                Textarea::make('notes')
                    ->label('Note Specifiche')
                    ->placeholder('Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")'),
            ]);
    }
}
