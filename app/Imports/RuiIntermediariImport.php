<?php

namespace App\Imports;

use App\Models\Rui;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiIntermediariImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
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

        $dataInizioInoperativita = !empty($row['data_inizio_inoperativita']) ? Carbon::createFromFormat('d/m/Y', $row['data_inizio_inoperativita'])->format('Y-m-d') : null;
        $dataIscrizione = !empty($row['data_iscrizione']) ? Carbon::createFromFormat('d/m/Y', $row['data_iscrizione'])->format('Y-m-d') : null;
        $dataNascita = !empty($row['data_nascita']) ? Carbon::createFromFormat('d/m/Y', $row['data_nascita'])->format('Y-m-d') : null;

        return new Rui([
            'oss' => $row['oss'] ?? '',
            'inoperativo' => !empty($row['inoperativo']) ? (bool) $row['inoperativo'] : false,
            'data_inizio_inoperativita' => $dataInizioInoperativita,
            'numero_iscrizione_rui' => $row['numero_iscrizione_rui'] ?? '',
            'data_iscrizione' => $dataIscrizione,
            'cognome_nome' => $row['cognome_nome'] ?? '',
            'stato' => $row['stato'] ?? '',
            'comune_nascita' => $row['comune_nascita'] ?? '',
            'data_nascita' => $dataNascita,
            'ragione_sociale' => $row['ragione_sociale'] ?? '',
            'provincia_nascita' => $row['provincia_nascita'] ?? '',
            'titolo_individuale_sez_a' => $row['titolo_individuale_sez_a'] ?? '',
            'attivita_esercitata_sez_a' => $row['attivita_esercitata_sez_a'] ?? '',
            'titolo_individuale_sez_b' => $row['titolo_individuale_sez_b'] ?? '',
            'attivita_esercitata_sez_b' => $row['attivita_esercitata_sez_b'] ?? '',
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
