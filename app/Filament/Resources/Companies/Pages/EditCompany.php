<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use App\Models\Agent;
use App\Models\Principal;
use App\Models\PrincipalEmployee;
use App\Models\Rui;
use App\Models\RuiCollaboratori;
// use App\Models\RuiIntermediari;
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
                    $ruiSede = RuiSedi::where('numero_iscrizione_int', $companyIscrizioneRui)->first();
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
                    }

                    // Find RUI collaborator with matching registration number
                    $ruiCollaborators = RuiCollaboratori::where('num_iscr_collaboratori_i_liv', $companyIscrizioneRui)->get();

                    if ($ruiCollaborators->isEmpty()) {
                        Notification::make()
                            ->title('Nessun OAM trovato')
                            ->body('Nessun intermediatore trovato con il numero di iscrizione specificato')
                            ->warning()
                            ->send();
                        return;
                    }

                    foreach ($ruiCollaborators as $ruiCollaborator) {
                        // 1. FIX: Usa === per il confronto e la freccia -> per l'oggetto (dato che hai usato ->get())
                        \Log::info('Intermediario trovato', ['Rui collab' => $ruiCollaborator]);
                        if ($ruiCollaborator->livello === 'I') {
                            $ruiIntermediario = $ruiCollaborator->num_iscr_intermediario;
                            $ruiIntermediario6 = substr($ruiIntermediario, -6);

                            // Cerca l'intermediario
                            $intermediario = Rui::where('numero_iscrizione_rui', 'like', '%' . $ruiIntermediario6)->first();
                            \Log::info('Intermediario cercato', ['intermediario' => $ruiIntermediario6]);
                            if ($intermediario) {
                                // Cerca o crea il Principal
                                \Log::info('Intermediario trovato', ['intermediario' => $intermediario]);
                                $principal = Principal::where('numero_iscrizione_rui', $ruiIntermediario)
                                    ->orWhere('name', $intermediario->ragione_sociale)
                                    ->orWhere('oam_name', $intermediario->ragione_sociale)
                                    ->first();

                                if (!$principal) {
                                    $principal = Principal::create([
                                        'name' => $intermediario->ragione_sociale,
                                        'numero_iscrizione_rui' => $ruiIntermediario,  // Assicurati di salvarlo subito
                                    ]);
                                }
                                \Log::info('Aggiorno', ['principal' => $principal]);
                                // Aggiorna dati OAM
                                $principal->update([
                                    'oam_name' => $intermediario->ragione_sociale,
                                    'oam_at' => $intermediario->data_iscrizione,
                                    'numero_iscrizione_rui' => $ruiIntermediario,
                                ]);

                                // 2. FIX: Controllo esistenza variabile $ruiSede prima dell'uso
                                // Cerchiamo la sede associata all'intermediario
                                $ruiSede = RuiSedi::where('numero_iscrizione_int', $ruiIntermediario)->first();

                                if ($ruiSede) {
                                    // Aggiorna o crea l'indirizzo di tipo 5 (Legale)
                                    $principal->addresses()->updateOrCreate(
                                        ['address_type_id' => 5],  // Criterio di ricerca
                                        [
                                            'name' => 'Legale',
                                            'street' => $ruiSede->indirizzo_sede,
                                            'city' => $ruiSede->comune_sede,
                                            'province' => $ruiSede->provincia_sede,
                                            'zip_code' => $ruiSede->cap_sede,
                                        ]
                                    );
                                }
                            }
                        }

                        // 3. Gestione Livello II
                        if ($ruiCollaborator->livello === 'II') {
                            $ruiAgente = $ruiCollaborator->num_iscr_collaboratori_ii_liv;

                            if ($ruiAgente) {
                                $agent = Agent::where('numero_iscrizione_rui', $ruiAgente)->first();

                                if ($agent) {
                                    $ruiIntermediario = $ruiCollaborator->num_iscr_intermediario;
                                    $principal = Principal::where('numero_iscrizione_rui', $ruiIntermediario)->first();

                                    if ($principal) {
                                        // Crea relazione collaboratore
                                        PrincipalEmployee::firstOrCreate(
                                            [
                                                'principal_id' => $principal->id,
                                                'personable_type' => Agent::class,
                                                'personable_id' => $agent->id,
                                                'company_id' => $company->id,
                                            ],
                                            [
                                                'num_iscr_intermediario' => $ruiIntermediario,
                                                'num_iscr_collaboratori_ii_liv' => $ruiAgente,
                                                'description' => $ruiCollaborator->qualifica_rapporto,
                                                'start_date' => now(),
                                                'is_active' => true,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    }
                    Notification::make()
                        ->title('OAM abbinate con successo')
                        ->body('OAM abbinate con successo')
                        ->success()
                        ->send();
                }),
            // DeleteAction::make(),
        ];
    }
}
