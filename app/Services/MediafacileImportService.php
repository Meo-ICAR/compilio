<?php

namespace App\Services;

use App\Models\Fornitore;
use App\Models\Pratica;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MediafacileImportService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        // Meglio usare config() invece di env() direttamente nel codice
        $this->apiUrl = config('services.mediafacile.url', env('MEDIAFACILE_BASE_URL', 'https://races.mediafacile.it/ws/hassisto.php'));
        $this->apiKey = config('services.mediafacile.key', env('MEDIAFACILE_HEADER_KEY'));
    }

    /**
     * Esegue l'importazione delle pratiche
     */
    public function import(Carbon $startDate, Carbon $endDate): array
    {
        $result = [
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'success' => true,
            'message' => 'Importazione completata'
        ];

        try {
            $data = $this->fetchData($startDate, $endDate);

            if (empty($data)) {
                $result['message'] = 'Nessun record trovato nel range di date specificato.';
                return $result;
            }

            foreach ($data as $item) {
                try {
                    $praticaData = $this->mapApiToModel($item);

                    if (empty($praticaData['id'])) {
                        Log::warning('Skipping item without id: ' . json_encode($item));
                        $result['errors']++;
                        continue;
                    }

                    $this->processRecord($praticaData);

                    // Se non ha lanciato eccezioni, consideriamo il record processato
                    // Un modo semplice per sapere se è stato aggiornato o creato:
                    if (Pratica::where('id', $praticaData['id'])->exists()) {
                        $result['updated']++;  // Nota: questo conteggio è approssimativo se fai updateOrCreate
                    } else {
                        $result['imported']++;
                    }
                } catch (Exception $e) {
                    Log::error('Error processing item: ' . $e->getMessage(), ['item' => $item]);
                    $result['errors']++;
                }
            }
        } catch (Exception $e) {
            Log::error("Errore critico durante l'importazione Pratiche: " . $e->getMessage());
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Recupera e formatta i dati dall'API
     */
    protected function fetchData(Carbon $startDate, Carbon $endDate): array
    {
        $queryParams = [
            'table' => 'pratiche',
            'data_inizio' => $startDate->format('Y-m-d'),
            'data_fine' => $endDate->format('Y-m-d'),
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json, */*',
            'User-Agent' => 'ProForma Import/1.0',
            'X-Api-Key' => $this->apiKey,
        ])
            ->timeout(60)
            ->connectTimeout(10)
            ->withOptions(['http_errors' => false, 'verify' => false])
            ->retry(3, 1000, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                    ($exception->getCode() >= 500);
            })
            ->get($this->apiUrl, $queryParams);

        if (!$response->successful()) {
            throw new Exception('API request failed with status: ' . $response->status());
        }

        return $this->parseTsv($response->body());
    }

    /**
     * Converte il body TSV in un array associativo
     */
    protected function parseTsv(string $body): array
    {
        $lines = array_values(array_filter(explode("\n", trim($body)), function ($line) {
            return trim($line) !== '';
        }));

        if (empty($lines))
            return [];

        $headers = $this->parseLine($lines[0]);
        $data = [];

        for ($i = 1; $i < count($lines); $i++) {
            $values = $this->parseLine($lines[$i]);
            if (count($values) === count($headers)) {
                $data[] = array_combine($headers, $values);
            }
        }

        return $data;
    }

    protected function parseLine(string $line): array
    {
        return array_map('trim', explode("\t", $line));
    }

    /**
     * Salva o aggiorna il record a DB
     */
    protected function processRecord(array $praticaData): void
    {
        $existing = Pratica::where('id', $praticaData['id'])->first();

        if ($existing) {
            $existing->update($praticaData);
        } else {
            // Gestione del Fornitore
            if (!empty($praticaData['denominazione_agente'])) {
                Fornitore::firstOrCreate(
                    ['name' => $praticaData['denominazione_agente']],
                    ['piva' => $praticaData['partita_iva_agente']]
                );
            }
            Pratica::create($praticaData);
        }
    }

    /**
     * Mappa i dati grezzi dell'API nel formato del Model
     */
    protected function mapApiToModel(array $apiData): array
    {
        $dataInserimento = null;
        $dataInserimentoValue = $apiData['Data Inserimento Pratica'] ?? null;

        if (!empty($dataInserimentoValue)) {
            try {
                $dateParts = explode('/', $dataInserimentoValue);
                if (count($dateParts) === 3) {
                    $dataInserimento = Carbon::createFromFormat('d/m/Y', $dataInserimentoValue);
                }
            } catch (Exception $e) {
                Log::warning('Failed to parse date: ' . $dataInserimentoValue);
            }
        }

        return [
            'id' => $apiData['ID Pratica'] ?? (string) Str::uuid(),
            'codice_pratica' => $apiData['ID Pratica'] ?? null,
            'nome_cliente' => $apiData['Cognome Cliente'] ?? null,
            'cognome_cliente' => $apiData['Nome Cliente'] ?? null,
            'codice_fiscale' => $apiData['Codice Fiscale'] ?? null,
            'denominazione_agente' => $apiData['Denominazione Agente'] ?? null,
            'partita_iva_agente' => (blank($apiData['Partita IVA Agente'] ?? null) || $apiData['Partita IVA Agente'] < '0')
                ? '---'
                : $apiData['Partita IVA Agente'],
            'denominazione_banca' => $apiData['Denominazione Banca'] ?? null,
            'tipo_prodotto' => $apiData['Tipo Prodotto'] ?? null,
            'denominazione_prodotto' => $apiData['Descrizione Prodotto'] ?? null,
            'data_inserimento_pratica' => $dataInserimento ?? now(),
            'stato_pratica' => $apiData['Stato Pratica'] ?? null,
        ];
    }
}
