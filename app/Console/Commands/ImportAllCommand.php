<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all {--start-date=2024-01-01 : Data di inizio per le importazioni}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Esegue tutte le importazioni (pratiche, provvigioni, fatture vendita, fatture acquisto, OAM)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Avvio importazione completa di tutti i dati...');
        $this->info('📅 Data di inizio: ' . $this->option('start-date'));
        $this->newLine();

        $startDate = $this->option('start-date');

        $commands = [
            [
                'command' => 'pratiche:import-api',
                'params' => ["--start-date={$startDate}"],
                'description' => 'Pratiche',
                'icon' => '📋'
            ],
            [
                'command' => 'provvigioni:import-api',
                'params' => ["--start-date={$startDate}"],
                'description' => 'Provvigioni',
                'icon' => '💰'
            ],
            [
                'command' => 'sales-invoices:import',
                'params' => [],
                'description' => 'Fatture di Vendita',
                'icon' => '📤'
            ],
            [
                'command' => 'purchase-invoices:import',
                'params' => [],
                'description' => 'Fatture di Acquisto',
                'icon' => '📥'
            ],
            [
                'command' => 'oam:import',
                'params' => [],
                'description' => 'OAM',
                'icon' => '🏛️'
            ]
        ];

        $totalStartTime = microtime(true);
        $successCount = 0;
        $errorCount = 0;

        foreach ($commands as $index => $cmd) {
            $this->line("{$cmd['icon']} [{$index}/" . count($commands) . "] Importazione {$cmd['description']}...");

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
                    $this->info("✅ {$cmd['description']} importato con successo in {$executionTime}s");
                    $successCount++;
                } else {
                    $this->error("❌ Errore nell'importazione {$cmd['description']} (Exit code: {$exitCode})");
                    $errorCount++;

                    // Mostra output di errore
                    $output = Artisan::output();
                    if (!empty(trim($output))) {
                        $this->line('Errore: ' . trim($output));
                    }
                }
            } catch (\Exception $e) {
                $this->error("❌ Eccezione durante l'importazione {$cmd['description']}: " . $e->getMessage());
                $errorCount++;
            }

            // Aggiungi una pausa tra i comandi
            if ($index < count($commands) - 1) {
                $this->line('⏳ Pausa di 2 secondi...');
                sleep(2);
            }

            $this->newLine();
        }

        $totalEndTime = microtime(true);
        $totalExecutionTime = round($totalEndTime - $totalStartTime, 2);

        // Riepilogo finale
        $this->newLine();
        $this->info('📊 RIEPILOGO IMPORTAZIONE');
        $this->info('========================');
        $this->info("✅ Importazioni riuscite: {$successCount}");
        $this->info("❌ Importazioni fallite: {$errorCount}");
        $this->info("⏱️ Tempo totale: {$totalExecutionTime}s");

        if ($errorCount === 0) {
            $this->newLine();
            $this->info('🎉 Tutte le importazioni sono state completate con successo!');
        } else {
            $this->newLine();
            $this->warn('⚠️ Alcune importazioni hanno riscontrato errori. Controlla i log per dettagli.');
        }

        return $errorCount === 0 ? 0 : 1;
    }
}
