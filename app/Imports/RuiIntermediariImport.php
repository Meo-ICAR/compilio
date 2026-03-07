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
    protected $limit = 99999999;  // Limit to 99999999 records for production
    protected $debug = false;
    protected $fileName = '';

    public function __construct($limit = 99999999, $debug = false, $fileName = '')
    {
        $this->limit = $limit;
        $this->debug = $debug;
        $this->fileName = $fileName;
    }

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
        // Stop if we've reached the limit
        if ($this->importedCount >= $this->limit) {
            return null;
        }

        if ($this->debug && $this->importedCount % 1000 === 0) {
            $fileNameDisplay = $this->fileName ? " ({$this->fileName})" : '';
            echo "📊 Processed {$this->importedCount} records{$fileNameDisplay}...\n";
        }

        $this->importedCount++;

        $dataInizioInoperativita = !empty($row['data_inizio_inoperativita']) ? Carbon::createFromFormat('d/m/Y', $row['data_inizio_inoperativita'])->format('Y-m-d') : null;
        $dataIscrizione = !empty($row['data_iscrizione']) ? Carbon::createFromFormat('d/m/Y', $row['data_iscrizione'])->format('Y-m-d') : null;
        $dataNascita = !empty($row['data_nascita']) ? Carbon::createFromFormat('d/m/Y', $row['data_nascita'])->format('Y-m-d') : null;

        $numeroIscrizioneRui = $row['numero_iscrizione_rui'] ?? '';

        // Find existing record or create new one
        $rui = Rui::where('numero_iscrizione_rui', $numeroIscrizioneRui)->first();

        if ($rui) {
            // Update existing record
            $rui->update([
                'oss' => $row['oss'] ?? '',
                'inoperativo' => !empty($row['inoperativo']) ? (bool) $row['inoperativo'] : false,
                'data_inizio_inoperativita' => $dataInizioInoperativita,
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
                'updated_at' => now(),
            ]);
            return null;  // Return null to prevent Laravel Excel from trying to insert
        } else {
            // Create new record
            return new Rui([
                'numero_iscrizione_rui' => $numeroIscrizioneRui,
                'oss' => $row['oss'] ?? '',
                'inoperativo' => !empty($row['inoperativo']) ? (bool) $row['inoperativo'] : false,
                'data_inizio_inoperativita' => $dataInizioInoperativita,
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function batchSize(): int
    {
        return 1000;  // Larger batch for production
    }

    public function chunkSize(): int
    {
        return 2000;  // Larger chunk for production
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
