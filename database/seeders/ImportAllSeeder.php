<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ImportAllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Inizio importazione completa di tutti i dati...');

        $commands = [
            [
                'command' => 'pratiche:import-api',
                'params' => ['--start-date=2024-01-01'],
                'description' => 'Pratiche'
            ],
            [
                'command' => 'provvigioni:import-api',
                'params' => ['--start-date=2024-01-01'],
                'description' => 'Provvigioni'
            ],
            [
                'command' => 'sales-invoices:import',
                'params' => [],
                'description' => 'Fatture di Vendita'
            ],
            [
                'command' => 'purchase-invoices:import',
                'params' => [],
                'description' => 'Fatture di Acquisto'
            ],
            [
                'command' => 'oam:import',
                'params' => [],
                'description' => 'OAM'
            ]
        ];

        foreach ($commands as $index => $cmd) {
            $this->command->info('[' . ($index + 1) . "/5] Importazione {$cmd['description']}...");

            try {
                $startTime = microtime(true);

                // Costruisci il comando con i parametri
                $fullCommand = $cmd['command'];
                if (!empty($cmd['params'])) {
                    $fullCommand .= ' ' . implode(' ', $cmd['params']);
                }

                // Esegui il comando Artisan
                $exitCode = Artisan::call($fullCommand);

                $endTime = microtime(true);
                $executionTime = round($endTime - $startTime, 2);

                if ($exitCode === 0) {
                    $this->command->info("✅ {$cmd['description']} importato con successo in {$executionTime}s");
                    Log::info("Import {$cmd['description']} completed successfully", [
                        'command' => $cmd['command'],
                        'execution_time' => $executionTime
                    ]);
                } else {
                    $this->command->error("❌ Errore nell'importazione {$cmd['description']} (Exit code: {$exitCode})");
                    Log::error("Import {$cmd['description']} failed", [
                        'command' => $cmd['command'],
                        'exit_code' => $exitCode,
                        'output' => Artisan::output()
                    ]);

                    // Continua con gli altri comandi anche se uno fallisce
                    continue;
                }

                // Mostra output del comando se c'è
                $output = Artisan::output();
                if (!empty(trim($output))) {
                    $this->command->line('Output: ' . trim($output));
                }
            } catch (\Exception $e) {
                $this->command->error("❌ Eccezione durante l'importazione {$cmd['description']}: " . $e->getMessage());
                Log::error("Exception during import {$cmd['description']}", [
                    'command' => $cmd['command'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Continua con gli altri comandi
                continue;
            }

            // Aggiungi una pausa tra i comandi per non sovraccaricare il sistema
            $this->command->info('⏳ Pausa di 2 secondi...');
            sleep(2);
        }

        $this->command->info('🎉 Importazione completa terminata!');
        Log::info('All imports completed successfully');
    }
}
