<?php

namespace App\Services;

use App\Models\Rui;
use App\Models\RuiAccessoris;
use App\Models\RuiAgentis;
use App\Models\RuiCariche;
use App\Models\RuiCollaboratori;
use App\Models\RuiIntermediaris;
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
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    /**
     * Debug: Import only 10 records per file and verify data
     *
     * @return array Import results
     */
    public function debugImportTenRecords(): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [],
                'verification' => []
            ];

            $ruiDirectory = public_path('RUI');
            $csvFiles = glob($ruiDirectory . '/*.csv');

            foreach ($csvFiles as $filePath) {
                $fileName = basename($filePath, '.csv');

                Log::info("Debug: Processing {$fileName} - 10 records only");

                $this->processCsvFileWithLimit($filePath, $fileName, $results, 10);
                $results['files_processed']++;

                // Verify the imported data
                $this->verifyImportedData($fileName, $results);
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Debug import failed: ' . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()],
                'verification' => []
            ];
        }
    }

    /**
     * Process CSV file with record limit
     *
     * @param string $filePath
     * @param string $fileName
     * @param array $results
     * @param int $limit
     * @return void
     */
    private function processCsvFileWithLimit(string $filePath, string $fileName, array &$results, int $limit = 10): void
    {
        try {
            $importClass = $this->getImportClass($fileName);
            if (!$importClass) {
                $results['errors'][] = "No import class found for file: {$fileName}";
                return;
            }

            $import = new $importClass();

            // Create a limited import by reading only first few rows
            $this->importLimitedRows($import, $filePath, $limit);

            $results['records_imported'] += $import->getImportedCount();

            Log::info("Debug: Processed {$fileName} - {$import->getImportedCount()} records");
        } catch (\Exception $e) {
            $results['errors'][] = "Error processing {$fileName}: " . $e->getMessage();
        }
    }

    /**
     * Import limited rows from CSV
     *
     * @param mixed $import
     * @param string $filePath
     * @param int $limit
     * @return void
     */
    private function importLimitedRows($import, string $filePath, int $limit): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open file: {$filePath}");
        }

        // Skip header
        fgetcsv($handle, 1000, ';');

        $importedCount = 0;
        while (($row = fgetcsv($handle, 1000, ';')) !== false && $importedCount < $limit) {
            // Convert to associative array with lowercase keys
            $headerRow = $this->getCsvHeaders($filePath);
            $assocRow = [];
            foreach ($headerRow as $index => $header) {
                $assocRow[strtolower($header)] = $row[$index] ?? '';
            }

            // Call the model method and save the result
            $model = $import->model($assocRow);
            if ($model) {
                $model->save();
            }
            $importedCount++;
        }

        fclose($handle);
    }

    /**
     * Get CSV headers
     *
     * @param string $filePath
     * @return array
     */
    private function getCsvHeaders(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle, 1000, ';');
        fclose($handle);
        return $headers ?: [];
    }

    /**
     * Verify imported data for a table
     *
     * @param string $fileName
     * @param array $results
     * @return void
     */
    private function verifyImportedData(string $fileName, array &$results): void
    {
        $tableMap = [
            'ELENCO_SITO_INTERNET' => 'rui_websites',
            'ELENCO_MANDATI' => 'rui_mandati',
            'ELENCO_COLLABORATORI' => 'rui_collaboratori',
            'ELENCO_COLLABACCESSORI' => 'rui_accessoris',
            'ELENCO_INTERMEDIARI' => 'rui',
            'ELENCO_SEDI' => 'rui_sedi',
            'ELENCO_AG_VEN_PROD_NONST_ISCR_S' => 'rui_agentis',
            'ELENCO_RESP_DISTRIB_SEZ_D' => 'rui_sezds',
            'ELENCO_CARICHE' => 'rui_cariche',
        ];

        $tableName = $tableMap[$fileName] ?? null;
        if (!$tableName) {
            return;
        }

        try {
            $modelClass = $this->getModelClass($tableName);
            if (!$modelClass) {
                return;
            }

            $count = $modelClass::count();
            $firstRecord = $modelClass::first();

            $verification = [
                'table' => $tableName,
                'total_records' => $count,
                'first_record_data' => $firstRecord ? $this->getRecordData($firstRecord) : null,
                'has_data' => $count > 0 && $this->recordHasData($firstRecord)
            ];

            $results['verification'][] = $verification;

            Log::info("Verification for {$tableName}: {$count} records, has_data: " . ($verification['has_data'] ? 'YES' : 'NO'));
        } catch (\Exception $e) {
            $results['errors'][] = "Verification error for {$tableName}: " . $e->getMessage();
        }
    }

    /**
     * Get model class for table name
     *
     * @param string $tableName
     * @return string|null
     */
    private function getModelClass(string $tableName): ?string
    {
        $modelMap = [
            'rui' => 'App\Models\Rui',
            'rui_sedi' => 'App\Models\RuiSedi',
            'rui_mandati' => 'App\Models\RuiMandati',
            'rui_cariche' => 'App\Models\RuiCariche',
            'rui_collaboratori' => 'App\Models\RuiCollaboratori',
            'rui_accessoris' => 'App\Models\RuiAccessoris',
            'rui_agentis' => 'App\Models\RuiAgentis',
            'rui_sezds' => 'App\Models\RuiSezds',
            'rui_websites' => 'App\Models\RuiWebsite',
            'rui_intermediaris' => 'App\Models\RuiIntermediaris',
        ];

        return $modelMap[$tableName] ?? null;
    }

    /**
     * Get record data as array
     *
     * @param mixed $record
     * @return array
     */
    private function getRecordData($record): array
    {
        return $record->toArray();
    }

    /**
     * Check if record has actual data (not just empty strings)
     *
     * @param mixed $record
     * @return bool
     */
    private function recordHasData($record): bool
    {
        if (!$record) {
            return false;
        }

        $data = $record->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);

        // Check if any field has non-empty data
        foreach ($data as $value) {
            if ($value !== null && $value !== '' && $value !== '0') {
                return true;
            }
        }

        return false;
    }

    /**
     * Debug: Import only RUI file
     *
     * @return array Import results
     */
    public function debugImportRuiOnly(): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => []
            ];

            $filePath = public_path('RUI/ELENCO_INTERMEDIARI.csv');
            $fileName = 'ELENCO_INTERMEDIARI';

            Log::info("Debug: Processing RUI file only - {$filePath}");

            $this->processCsvFile($filePath, $fileName, $results);
            $results['files_processed']++;

            Log::info("Debug: RUI import completed - {$results['records_imported']} records");

            return $results;
        } catch (\Exception $e) {
            Log::error('RUI debug import failed: ' . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

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
     * Process individual CSV file using Laravel Excel
     *
     * @param string $filePath
     * @param string $fileName
     * @param array $results
     * @return void
     */
    private function processCsvFile(string $filePath, string $fileName, array &$results): void
    {
        try {
            $importClass = $this->getImportClass($fileName);
            if (!$importClass) {
                $results['errors'][] = "No import class found for file: {$fileName}";
                return;
            }

            $import = new $importClass();
            Excel::import($import, $filePath);

            $results['records_imported'] += $import->getImportedCount();

            Log::info("Processed {$fileName}: {$import->getImportedCount()} records");
        } catch (\Exception $e) {
            $results['errors'][] = "Error processing {$fileName}: " . $e->getMessage();
        }
    }

    /**
     * Get the appropriate import class for a CSV file
     *
     * @param string $fileName
     * @return string|null
     */
    private function getImportClass(string $fileName): ?string
    {
        $importClasses = [
            'ELENCO_SITO_INTERNET' => 'App\Imports\RuiWebsitesImport',
            'ELENCO_MANDATI' => 'App\Imports\RuiMandatiImport',
            'ELENCO_COLLABORATORI' => 'App\Imports\RuiCollaboratoriImport',
            'ELENCO_COLLABACCESSORI' => 'App\Imports\RuiAccessorisImport',
            'ELENCO_INTERMEDIARI' => 'App\Imports\RuiIntermediariImport',
            'ELENCO_SEDI' => 'App\Imports\RuiSediImport',
            'ELENCO_AG_VEN_PROD_NONST_ISCR_S' => 'App\Imports\RuiAgentisImport',
            'ELENCO_RESP_DISTRIB_SEZ_D' => 'App\Imports\RuiSezdsImport',
            'ELENCO_CARICHE' => 'App\Imports\RuiCaricheImport',
        ];

        return $importClasses[$fileName] ?? null;
    }

    /**
     * Get import statistics for all RUI tables
     *
     * @return array Statistics for each table
     */
    public function getImportStatistics(): array
    {
        return [
            'rui_sedi' => RuiSedi::count(),
            'rui_mandati' => RuiMandati::count(),
            'rui_cariche' => RuiCariche::count(),
            'rui_collaboratori' => RuiCollaboratori::count(),
            'rui_accessoris' => RuiAccessoris::count(),
            'rui_agentis' => RuiAgentis::count(),
            'rui_sezds' => RuiSezds::count(),
            'rui_intermediaris' => RuiIntermediaris::count(),
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
            RuiIntermediaris::truncate();
            Rui::truncate();

            return ['success' => true, 'message' => 'All RUI data cleared successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to clear RUI data: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
