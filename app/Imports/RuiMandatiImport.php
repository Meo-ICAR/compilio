<?php

namespace App\Imports;

use App\Models\RuiMandati;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiMandatiImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
{
    protected $importedCount = 0;

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'escape' => '\\',
            'inputEncoding' => 'UTF-8',
        ];
    }

    public function model(array $row)
    {
        $this->importedCount++;

        // Debug: Log the first few rows to see what data we're getting
        if ($this->importedCount <= 3) {
            \Log::info("Debug RuiMandati Row {$this->importedCount}: " . json_encode($row, JSON_UNESCAPED_UNICODE));
        }

        return new RuiMandati([
            'oss' => $row['oss'] ?? '',
            'matricola' => $row['matricola'] ?? '',
            'codice_compagnia' => $row['codice_compagnia'] ?? '',
            'ragione_sociale' => $row['ragione_sociale'] ?? '',
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
