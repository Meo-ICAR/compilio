<?php

namespace App\Observers;

use App\Models\AuiLog;
use App\Models\Practice;

class PraticaObserver
{
    public function saved(Practice $pratica): void
    {
        // ESECUZIONE OPERAZIONE (Quando la singola richiesta in banca passa a "erogata")
        if ($pratica->wasChanged('erogated_at')) {
            // 1. Registriamo l'esecuzione in AUI
            $this->registraEsecuzioneAui($pratica);

            // 2. AUTOMAZIONE MAGICA: Se questa pratica è erogata, chiudiamo il mandato padre!
            // (Questo triggererà automaticamente il ClientMandateObserver per fare la Chiusura AUI)
            if ($pratica->mandato && $pratica->mandato->stato !== 'concluso_con_successo') {
                $pratica->mandato->update(['stato' => 'concluso_con_successo']);
            }
        }
    }

    private function registraEsecuzioneAui(Practice $pratica): void
    {
        $esiste = AuiLog::where('practice_id', $pratica->id)
            ->where('tipo_evento', 'esecuzione_operazione')
            ->exists();

        if (!$esiste) {
            AuiLog::create([
                'client_mandate_id' => $pratica->client_mandate_id,
                'practice_id' => $pratica->id,  // Qui salviamo l'ID della pratica vincente!
                'tipo_evento' => 'esecuzione_operazione',
                // Ricorda: per i mutui è la data_stipula, per le cessioni è data_liquidazione
                'data_evento' => $pratica->data_erogazione,
                'importo_rilevato' => $pratica->importo_erogato,
                'stato' => 'da_consolidare',
                'payload_dati_cliente' => $pratica->mandato->cliente->toArray(),
            ]);
        }
    }
}
