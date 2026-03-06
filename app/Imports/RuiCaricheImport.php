<?php

namespace App\Imports;

use App\Models\RuiCariche;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiCaricheImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
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

        return new RuiCariche([
            'oss' => $row['oss'] ?? '',
            'numero_iscrizione_rui_pf' => $row['numero_iscrizione_rui_pf'] ?? '',
            'numero_iscrizione_rui_pg' => $row['numero_iscrizione_rui_pg'] ?? '',
            'qualifica_intermediario' => $row['qualifica_intermediario'] ?? '',
            'responsabile' => $row['responsabile'] ?? '',
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
