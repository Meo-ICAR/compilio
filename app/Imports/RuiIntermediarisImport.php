<?php

namespace App\Imports;

use App\Models\RuiIntermediaris;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiIntermediarisImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
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
            \Log::info("Debug RuiIntermediaris Row {$this->importedCount}: " . json_encode($row, JSON_UNESCAPED_UNICODE));
        }

        return new RuiIntermediaris([
            'oss' => $row['OSS'] ?? '',
            'matricola' => $row['MATRICOLA'] ?? '',
            'codice_compagnia' => $row['CODICE_COMPAGNIA'] ?? '',
            'ragione_sociale' => $row['RAGIONE_SOCIALE'] ?? '',
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
