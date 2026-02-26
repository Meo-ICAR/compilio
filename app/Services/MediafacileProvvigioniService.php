<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Company;
use App\Models\Practice;
use App\Models\PracticeCommission;
use App\Models\PracticeScope;
use App\Models\PracticeStatus;
use App\Models\Principal;
use App\Models\SoftwareApplication;
use App\Models\SoftwareMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MediafacileImportService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $softwareId;
    protected string $companyId;

    public function __construct()
    {
        // Meglio usare config() invece di env() direttamente nel codice
        $this->apiUrl = config('services.mediafacile.url', env('MEDIAFACILE_BASE_URL', 'https://races.mediafacile.it/ws/hassisto.php'));

        $this->apiKey = config('services.mediafacile.key', env('MEDIAFACILE_HEADER_KEY'));
        $this->companyId = Company::where('oam_name', 'RACES FINANCE SRL')->first()->id;

        $this->softwareId = SoftwareApplication::where('name', 'Mediafacile')->first()->id;
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
                    $provvigioneData = $this->mapApiToModel($item);

                    if (empty($provvigioneData['id'])) {
                        Log::warning('Skipping item without id: ' . json_encode($item));
                        $result['errors']++;
                        continue;
                    }

                    $this->processRecord($provvigioneData);

                    // Se non ha lanciato eccezioni, consideriamo il record processato
                    // Un modo semplice per sapere se è stato aggiornato o creato:
                    if (Pratica::where('id', $provvigioneData['id'])->exists()) {
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
            'table' => 'compensi',
            'data_inizio' => $startDate->format('Y-m-d'),
            'data_fine' => $endDate->format('Y-m-d'),
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json, */*',
            'User-Agent' => 'Compilio Import-Provvigioni/1.0',
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
    protected function processRecord(array $provvigioneData): void
    {
        $existing = PracticeCommission::where('CRM_code', $provvigioneData['CRM_code'])->first();

        if ($existing) {
            $existing->update($provvigioneData);
        } else {
            $provvigioneData['company_id'] = $this->companyId;
            // Software Mapping
            SoftwareMapping::firstOrCreate(
                ['software_application_id' => $this->softwareId, 'mapping_type' => 'PRACTICE_TYPE', 'external_value' => $provvigioneData['tipo_prodotto']],
                [
                    'name' => $provvigioneData['tipo_prodotto'],
                    'internal_id' => 1,  // Default ID, da mappare correttamente in base alla logica di business
                    'description' => 'Mapping automatico da Mediafacile',
                ]
            );
            SoftwareMapping::firstOrCreate(
                ['software_application_id' => $this->softwareId, 'mapping_type' => 'PRACTICE_STATUS', 'external_value' => $provvigioneData['stato_pratica']],
                [
                    'name' => $provvigioneData['stato_pratica'],
                    'internal_id' => 1,  // Default ID, da mappare correttamente in base alla logica di business
                    'description' => 'Mapping automatico da Mediafacile',
                ]
            );
            // Gestione cliente
            Client::firstOrCreate(
                ['tax_code' => $provvigioneData['codice_fiscale'], 'company_id' => $this->companyId],
                [
                    'first_name' => $provvigioneData['nome_cliente'],
                    'name' => $provvigioneData['cognome_cliente'],
                    'company_id' => $this->companyId,
                ]
            );

            // Gestione agente
            Agent::firstOrCreate(
                ['vat_number' => $provvigioneData['partita_iva_agente'], 'company_id' => $this->companyId],
                [
                    'name' => $provvigioneData['denominazione_agente'],
                    'company_id' => $this->companyId,
                ]
            );
            // Gestione della banca
            Principal::firstOrCreate(
                ['name' => $provvigioneData['denominazione_banca'], 'company_id' => $this->companyId],
                [
                    //  'company_id' => $this->companyId,
                ]
            );
            $rateData = $this->parseDescription($provvigioneData['denominazione_prodotto']);
            $provvigioneData['amount'] = $rateData['rata'] * $rateData['nrate'];
            //  $provvigioneData['nrate'] = $rateData['nrate'];
            Practice::create($provvigioneData);
        }
    }

    /**
     * Mappa i dati grezzi dell'API nel formato del Model
     */
    protected function mapApiToModel(array $apiData): array
    {
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

        // Parse all date fields
        $dataInserimento = $parseDate($apiData['Data Inserimento'] ?? null);
        $dataPagamento = $parseDate($apiData['Data Pagamento'] ?? null);
        $dataFattura = $parseDate($apiData['Data Fattura'] ?? null);
        $dataStatus = $parseDate($apiData['Data Status'] ?? null);

        return [
            'id' => $apiData['ID Compenso'] ?? null,
            'data_inserimento_compenso' => $dataInserimento ? $dataInserimento->toDateTimeString() : null,
            'descrizione' => $apiData['Descrizione'] ?? null,
            'tipo' => $apiData['Tipo'] ?? 'provvigione',
            'importo' => is_numeric($apiData['Importo']) ? $apiData['Importo'] : (is_string($apiData['Importo']) ? (float) str_replace(',', '.', $apiData['Importo']) : 0),
            'importo_effettivo' => is_numeric($apiData['Importo Effettivo'] ?? null) ? $apiData['Importo Effettivo'] : (is_string($apiData['Importo Effettivo'] ?? null) ? (float) str_replace(',', '.', $apiData['Importo Effettivo']) : null),
            'status_pagamento' => $apiData['Stato'] ?? '',
            'data_pagamento' => $dataPagamento ? $dataPagamento->toDateTimeString() : null,
            'n_fattura' => $apiData['N. Fattura'] ?? null,
            'data_fattura' => $dataFattura ? $dataFattura->toDateTimeString() : null,
            'data_status' => $dataStatus ? $dataStatus->toDateTimeString() : null,
            'status_compenso' => $apiData['Status Compenso'] ?? null,
            'denominazione_riferimento' => $apiData['Denominazione Riferimento'] ?? null,
            'entrata_uscita' => $apiData['Entrata Uscita'] ?? null,
            'id_pratica' => $apiData['ID Pratica'] ?? null,
            'segnalatore' => $apiData['Agente'] ?? null,
            'istituto_finanziario' => $apiData['Istituto finanziario'] ?? null,
            'piva' => !empty($apiData['Partita IVA Agente'])
                ? $apiData['Partita IVA Agente']
                : (!empty($apiData['Codice Fiscale Agente']) ? $apiData['Codice Fiscale Agente'] : null),
            'cf' => $apiData['Codice Fiscale Agente'] ?? null,
            // 'annullato' => !empty($apiData['ANNULLATA']) && $apiData['ANNULLATA'] === 'SI',
            //  'invoice_number' => $apiData['ANNULLATA'] ?? null,
            'fonte' => 'mediafacile',
            'coordinamento' => $apiData['Agente'] <> $apiData['Denominazione Riferimento'],
            'iscliente' => (isset($apiData['Descrizione']) && str_contains($apiData['Descrizione'], 'liente')),
        ];
    }

    private function parseDescription($description)
    {
        if (empty($description)) {
            return null;
        }

        // Pattern: "TipoProdotto - Banca - Rata x NRate (Codice)"
        // Esempi:
        // "Cessione - CAPITALFIN - 158 x 120 (QT06447)"
        // "Prestito - AGOS SPA - 155,36 x 60 (QT06446)"
        // "Delega -  - 100 x 84 (QT06438)"
        // "Mutuo - ING BANK - PRS - 599 x  (QT06440)"

        // Rimuovi il codice finale tra parentesi
        $description = preg_replace('/\s*\([^)]*\)\s*$/', '', $description);

        // Dividi per " - "
        $parts = explode(' - ', $description);

        $tipo_prodotto = trim($parts[0] ?? '');
        $denominazione_banca = '';
        $rata = null;
        $nrate = null;

        // Cerca la banca e i dati finanziari
        $remaining_parts = array_slice($parts, 1);

        foreach ($remaining_parts as $part) {
            $part = trim($part);

            // Se contiene "x" probabilmente è la parte finanziaria
            if (preg_match('/(\d+(?:,\d+)?)\s*x\s*(\d*)/', $part, $matches)) {
                $rata = str_replace(',', '.', $matches[1]);
                $nrate = !empty($matches[2]) ? (int) $matches[2] : null;
            } elseif (!empty($part) && !preg_match('/\d+\s*x/', $part)) {
                // È probabilmente il nome della banca
                $denominazione_banca = $part;
            }
        }

        // Se non abbiamo trovato la banca, prova a estrarla dal pattern completo
        if (empty($denominazione_banca) && preg_match('/^[^-]+-\s*([^-]+)-/', $description, $matches)) {
            $denominazione_banca = trim($matches[1]);
        }

        return [
            'tipo_prodotto' => $tipo_prodotto,
            'denominazione_banca' => $denominazione_banca,
            'rata' => $rata,
            'nrate' => $nrate,
        ];
    }
}
