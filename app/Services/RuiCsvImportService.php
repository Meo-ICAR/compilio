<?php

namespace App\Services;

use App\Models\Rui;
use App\Models\RuiAccessoris;
use App\Models\RuiAgentis;
use App\Models\RuiCariche;
use App\Models\RuiCollaboratori;
use App\Models\RuiMandati;
use App\Models\RuiSedi;
use App\Models\RuiSezds;
use App\Models\RuiWebsite;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RuiCsvImportService
{
    /**
     * Import all RUI CSV files from public/RUI directory
     *
     * @return array Import results
     */
    public function importAllRuiFiles(): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => []
            ];

            $ruiDirectory = public_path('RUI');
            $csvFiles = glob($ruiDirectory . '/*.csv');

            foreach ($csvFiles as $filePath) {
                $fileName = basename($filePath, '.csv');
                $this->processCsvFile($filePath, $fileName, $results);
                $results['files_processed']++;
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('RUI import failed: ' . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Process individual CSV file
     *
     * @param string $filePath
     * @param string $fileName
     * @param array $results
     * @return void
     */
    private function processCsvFile(string $filePath, string $fileName, array &$results): void
    {
        try {
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                $results['errors'][] = "Cannot open file: {$fileName}";
                return;
            }

            // Skip header row
            $header = fgetcsv($handle, 1000, ';');
            $recordsImported = 0;

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $this->processCsvRow($row, $fileName, $results);
                $recordsImported++;
            }

            fclose($handle);
            $results['records_imported'] += $recordsImported;

            Log::info("Processed {$fileName}: {$recordsImported} records");
        } catch (\Exception $e) {
            $results['errors'][] = "Error processing {$fileName}: " . $e->getMessage();
        }
    }

    /**
     * Process CSV row based on file type
     *
     * @param array $row
     * @param string $fileName
     * @param array $results
     * @return void
     */
    private function processCsvRow(array $row, string $fileName, array &$results): void
    {
        switch ($fileName) {
            case 'ELENCO_SITO_INTERNET.csv':
                $this->processWebsiteRow($row, $results);
                break;

            case 'ELENCO_MANDATI.csv':
                $this->processMandatiRow($row, $results);
                break;

            case 'ELENCO_COLLABORATORI.csv':
                $this->processCollaboratoriRow($row, $results);
                break;

            case 'ELENCO_COLLABACCESSORI.csv':
                $this->processAccessorisRow($row, $results);
                break;

            case 'ELENCO_INTERMEDIARI.csv':
                $this->processIntermediariRow($row, $results);
                break;

            case 'ELENCO_SEDI.csv':
                $this->processSediRow($row, $results);
                break;

            case 'ELENCO_AG_VEN_PROD_NONST_ISCR_S.csv':
                $this->processAgentisRow($row, $results);
                break;

            case 'ELENCO_RESP_DISTRIB_SEZ_D.csv':
                $this->processSezdsRow($row, $results);
                break;

            case 'ELENCO_CARICHE.csv':
                $this->processCaricheRow($row, $results);
                break;
        }
    }

    /**
     * Process website row
     */
    private function processWebsiteRow(array $row, array &$results): void
    {
        try {
            RuiWebsite::updateOrCreate(
                ['numero_iscrizione_rui' => $row[0] ?? ''],
                [
                    'web_url' => $row[1] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Website row error: ' . $e->getMessage();
        }
    }

    /**
     * Process mandati row
     */
    private function processMandatiRow(array $row, array &$results): void
    {
        try {
            RuiMandati::updateOrCreate(
                [
                    'oss' => $row[0] ?? '',
                    'matricola' => $row[1] ?? '',
                    'codice_compagnia' => $row[2] ?? '',
                ],
                [
                    'ragione_sociale' => $row[3] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Mandati row error: ' . $e->getMessage();
        }
    }

    /**
     * Process collaboratori row
     */
    private function processCollaboratoriRow(array $row, array &$results): void
    {
        try {
            RuiCollaboratori::updateOrCreate(
                [
                    'oss' => $row[0] ?? '',
                    'livello' => $row[1] ?? '',
                    'num_iscr_intermediario' => $row[2] ?? '',
                    'num_iscr_collaboratori_i_liv' => $row[3] ?? '',
                    'num_iscr_collaboratori_ii_liv' => $row[4] ?? '',
                ],
                [
                    'qualifica_rapporto' => $row[5] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Collaboratori row error: ' . $e->getMessage();
        }
    }

    /**
     * Process accessoris row
     */
    private function processAccessorisRow(array $row, array &$results): void
    {
        try {
            $dataNascita = !empty($row[4]) ? Carbon::parse($row[4])->format('Y-m-d') : null;

            RuiAccessoris::updateOrCreate(
                ['numero_iscrizione_e' => $row[0] ?? ''],
                [
                    'ragione_sociale' => $row[1] ?? '',
                    'cognome_nome' => $row[2] ?? '',
                    'sede_legale' => $row[3] ?? '',
                    'data_nascita' => $dataNascita,
                    'luogo_nascita' => $row[5] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Accessoris row error: ' . $e->getMessage();
        }
    }

    /**
     * Process intermediari row
     */
    private function processIntermediariRow(array $row, array &$results): void
    {
        try {
            $dataInizioInoperativita = !empty($row[2]) ? Carbon::parse($row[2])->format('Y-m-d') : null;
            $dataIscrizione = !empty($row[4]) ? Carbon::parse($row[4])->format('Y-m-d') : null;
            $dataNascita = !empty($row[8]) ? Carbon::parse($row[8])->format('Y-m-d') : null;

            Rui::updateOrCreate(
                ['numero_iscrizione_rui' => $row[3] ?? ''],
                [
                    'oss' => $row[0] ?? '',
                    'inoperativo' => !empty($row[1]) ? (bool) $row[1] : false,
                    'data_inizio_inoperativita' => $dataInizioInoperativita,
                    'data_iscrizione' => $dataIscrizione,
                    'cognome_nome' => $row[5] ?? '',
                    'stato' => $row[6] ?? '',
                    'comune_nascita' => $row[7] ?? '',
                    'data_nascita' => $dataNascita,
                    'ragione_sociale' => $row[9] ?? '',
                    'provincia_nascita' => $row[10] ?? '',
                    'titolo_individuale_sez_a' => $row[11] ?? '',
                    'attivita_esercitata_sez_a' => $row[12] ?? '',
                    'titolo_individuale_sez_b' => $row[13] ?? '',
                    'attivita_esercitata_sez_b' => $row[14] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Intermediari row error: ' . $e->getMessage();
        }
    }

    /**
     * Process sedi row
     */
    private function processSediRow(array $row, array &$results): void
    {
        try {
            RuiSedi::updateOrCreate(
                [
                    'oss' => $row[0] ?? '',
                    'numero_iscrizione_int' => $row[1] ?? '',
                    'tipo_sede' => $row[2] ?? '',
                ],
                [
                    'comune_sede' => $row[3] ?? '',
                    'provincia_sede' => $row[4] ?? '',
                    'cap_sede' => $row[5] ?? '',
                    'indirizzo_sede' => $row[6] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Sedi row error: ' . $e->getMessage();
        }
    }

    /**
     * Process agentis row
     */
    private function processAgentisRow(array $row, array &$results): void
    {
        try {
            $dataConferimento = !empty($row[2]) ? Carbon::parse($row[2])->format('Y-m-d H:i:s') : null;

            RuiAgentis::updateOrCreate(
                [
                    'numero_iscrizione_d' => $row[0] ?? '',
                    'numero_iscrizione_a' => $row[1] ?? '',
                    'codice_compagnia' => $row[3] ?? '',
                ],
                [
                    'data_conferimento' => $dataConferimento,
                    'ragione_sociale' => $row[4] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Agentis row error: ' . $e->getMessage();
        }
    }

    /**
     * Process sezds row
     */
    private function processSezdsRow(array $row, array &$results): void
    {
        try {
            RuiSezds::updateOrCreate(
                [
                    'numero_iscrizione_d' => $row[0] ?? '',
                    'ragione_sociale' => $row[1] ?? '',
                ],
                [
                    'cognome_nome_responsabile' => $row[2] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Sezds row error: ' . $e->getMessage();
        }
    }

    /**
     * Process cariche row
     */
    private function processCaricheRow(array $row, array &$results): void
    {
        try {
            RuiCariche::updateOrCreate(
                [
                    'oss' => $row[0] ?? '',
                    'numero_iscrizione_rui_pf' => $row[1] ?? '',
                    'numero_iscrizione_rui_pg' => $row[2] ?? '',
                ],
                [
                    'qualifica_intermediario' => $row[3] ?? '',
                    'responsabile' => $row[4] ?? '',
                ]
            );
        } catch (\Exception $e) {
            $results['errors'][] = 'Cariche row error: ' . $e->getMessage();
        }
    }

    /**
     * Get import statistics for all RUI tables
     *
     * @return array Statistics for each table
     */
    public function getImportStatistics(): array
    {
        return [
            'rui' => Rui::count(),
            'rui_sedi' => RuiSedi::count(),
            'rui_mandati' => RuiMandati::count(),
            'rui_cariche' => RuiCariche::count(),
            'rui_collaboratori' => RuiCollaboratori::count(),
            'rui_accessoris' => RuiAccessoris::count(),
            'rui_agentis' => RuiAgentis::count(),
            'rui_sezds' => RuiSezds::count(),
            'rui_websites' => RuiWebsite::count(),
        ];
    }

    /**
     * Clear all RUI data (for testing/reset purposes)
     *
     * @return array Results of the clearing operation
     */
    public function clearAllRuiData(): array
    {
        try {
            RuiWebsite::truncate();
            RuiSezds::truncate();
            RuiAgentis::truncate();
            RuiAccessoris::truncate();
            RuiCollaboratori::truncate();
            RuiCariche::truncate();
            RuiMandati::truncate();
            RuiSedi::truncate();
            Rui::truncate();

            return ['success' => true, 'message' => 'All RUI data cleared successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to clear RUI data: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
