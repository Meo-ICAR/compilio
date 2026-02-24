<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Imports\ClientsImporter;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl(url('images/default-avatar.png'))
                    ->toggleable(),
                IconColumn::make('is_person')
                    ->boolean(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('tax_code')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                IconColumn::make('is_pep')
                    ->boolean(),
                TextColumn::make('client_type_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_sanctioned')
                    ->numeric()
                    ->sortable(),
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
                Action::make('scarica_nomina_responsabile')
                    ->label('Nomina Privacy')
                    ->icon('heroicon-o-briefcase')
                    ->color('warning')
                    ->action(function ($record) {
                        // $record rappresenta il Partner/Fornitore
                        $company = $record->company;  // L'agenzia mandante
                        $tipoPartner = $record->clientType;  // La categoria dal tuo seeder

                        if (!$tipoPartner) {
                            Notification::make()->title('Errore')->body('Assegna prima una tipologia al partner.')->danger()->send();
                            return;
                        }

                        // Se è un Titolare Autonomo puro, avvisiamo l'utente (opzionale ma consigliato)
                        if (str_contains(strtolower($tipoPartner->privacy_role), 'titolare autonomo') && !str_contains(strtolower($tipoPartner->privacy_role), 'responsabile')) {
                            Notification::make()
                                ->title('Attenzione')
                                ->body('Questo profilo è un Titolare Autonomo (es. Notaio). Di norma non necessita di nomina ex Art. 28, ma di un accordo di contitolarità o condivisione dati.')
                                ->warning()
                                ->send();
                            // Puoi scegliere se bloccare con un "return;" o far scaricare comunque il PDF
                        }

                        // Gestione Logo in Base64
                        $logoBase64 = null;
                        if ($company->hasMedia('logo')) {
                            $media = $company->getFirstMedia('logo');
                            $path = $media->getPath();
                            if (file_exists($path)) {
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
                            }
                        }

                        // Generazione PDF
                        $pdf = Pdf::loadView('pdf.nomina-responsabile-esterno', [
                            'partner' => $record,
                            'company' => $company,
                            'profilo' => $tipoPartner,
                            'logoBase64' => $logoBase64,
                        ]);

                        $nomeFile = 'Nomina-Art28-' . Str::slug($record->name ?? $record->ragione_sociale) . '.pdf';

                        return response()->streamDownload(fn() => print ($pdf->stream()), $nomeFile);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(ClientsImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
