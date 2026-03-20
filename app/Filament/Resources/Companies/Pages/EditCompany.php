<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Principal;
use App\Models\Rui;
use App\Models\RuiCollaboratori;
use App\Models\RuiIntermediari;
use App\Models\RuiSedi;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('abbina_oam')
                ->label('Abbina OAM')
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->action(function () {
                    $company = $this->record;
                    $companyIscrizioneRui = $company->numero_iscrizione_rui;
                    $ruiSede = RuiSede::where('numero_iscrizione_int', $companyIscrizioneRui)->first();
                    if ($ruiSede) {
                        // Check if address with address_type_id => 5 already exists
                        $existingAddress = $company
                            ->addresses()
                            ->where('address_type_id', 5)
                            ->first();

                        if (!$existingAddress) {
                            $tipoSede = $ruiSede->tipo_sede;
                            $indirizzo_sede = $ruiSede->indirizzo_sede;
                            $comuneSede = $ruiSede->comune_sede;
                            $provinciaSede = $ruiSede->provincia_sede;
                            $capSede = $ruiSede->cap_sede;
                            $address = $company->addresses()->create([
                                'address_type_id' => 5,
                                'name' => 'Legale',
                                'street' => $indirizzo_sede,
                                'city' => $comuneSede,
                                'province' => $provinciaSede,
                                'zip_code' => $capSede,
                            ]);
                        }

                        $company->addresses->update();
                    }

                    // Find RUI collaborator with matching registration number and level 'I'
                    $ruiCollaborators = RuiCollaboratori::where('num_iscr_collaboratori_i_liv', $companyIscrizioneRui);

                    if (!$ruiCollaborators->exists()) {
                        Notification::make()
                            ->title('Nessun abbina OAM trovato')
                            ->body('Nessun collaboratore RUI trovato con il numero di iscrizione specificato e livello I')
                            ->warning()
                            ->send();
                        return;
                    }
                    foreach ($ruiCollaborators as $ruiCollaborator) {
                        if ($ruiCollaborator->livello === 'I') {
                            $ruiIntermediario = $ruiCollaborator->num_iscr_intermediario;
                            $intermediario = RuiIntermediari::where('numero_iscrizione', $ruiIntermediario)->first();

                            if ($intermediario) {
                                $principal = Principal::where('rui_intermediario_id', $ruiIntermediario)->first();
                                if (!$principal) {
                                    // cerca per ragione sociale oam
                                    $principal = Principal::where('name', $intermediario->ragione_sociale)->first();
                                    if (!$principal) {
                                        // crea nuovo principale
                                        $principal = Principal::create([
                                            'name' => $intermediario->ragione_sociale,
                                            'oam_name' => $intermediario->ragione_sociale,
                                            'oam_at' => $intermediario->data_iscrizione,
                                            'numero_iscrizione_rui' => $ruiIntermediario->$ruiIntermediario,
                                        ]);
                                    }
                                }
                                if ($principal) {
                                    $principal->update([
                                        'oam_name' => $intermediario->ragione_sociale,
                                        'oam_at' => $intermediario->data_iscrizione,
                                        'numero_iscrizione_rui' => $ruiIntermediario->$ruiIntermediario,
                                    ]);
                                    if ($ruiSede) {
                                        $ruiSede = RuiSede::where('numero_iscrizione_int', $ruiIntermediario)->first();
                                        $tipoSede = $ruiSede->tipo_sede;
                                        $indirizzo_sede = $ruiSede->indirizzo_sede;
                                        $comuneSede = $ruiSede->comune_sede;
                                        $provinciaSede = $ruiSede->provincia_sede;
                                        $capSede = $ruiSede->cap_sede;
                                        $existingAddress = $principal
                                            ->addresses()
                                            ->where('address_type_id', 5)
                                            ->first();

                                        if (!$existingAddress) {
                                            $address = $principal->addresses()->create([
                                                'address_type_id' => 5,
                                                'name' => 'Legale',
                                                'street' => $indirizzo_sede,
                                                'city' => $comuneSede,
                                                'province' => $provinciaSede,
                                                'zip_code' => $capSede,
                                            ]);
                                        }
                                    }
                                }

                                // cariche societarie dichiarate intermediario
                                //  $ruiCollaborator->num_iscr_intermediario = ruiCariche->numero_iscrizione_rui_pg;
                                //   ruiCariche->>numero_iscrizione_rui_pf = rui->cognome_nome
                            }

                            // Update company with OAM data from RUI collaborator
                        }
                        if ($ruiCollaborator->livello === 'II') {
                            Notification::make()
                                ->title('Abbbinamento OAM completato')
                                ->body("Azienda abbinate con successo agli intermediari: {$ruiCollaborator->cognome_nome}")
                                ->success()
                                ->send();
                        }
                    }
                }),
            // DeleteAction::make(),
        ];
    }
}
