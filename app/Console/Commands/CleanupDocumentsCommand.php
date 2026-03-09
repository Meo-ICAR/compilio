<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Console\Command;

class CleanupDocumentsCommand extends Command
{
    // Questa è la stringa che scrivi nel terminale
    protected $signature = 'documents:classify-existing';

    protected $description = 'Classifica i documenti esistenti usando le regex della tabella document_types';

    public function handle()
    {
        // 1. Recuperiamo tutti i tipi di documento con una regex definita
        $types = DocumentType::whereNotNull('regex')->orderBy('priority', 'desc')->get();

        if ($types->isEmpty()) {
            $this->error('Nessuna regex trovata nella tabella document_types. Hai lanciato il seeder?');
            return;
        }

        // 2. Prendiamo i documenti da classificare (quelli con ID 47 o null)
        $documents = Document::whereIn('document_type_id', [47])
            ->orWhereNull('document_type_id')
            ->get();

        $this->info("Analisi di {$documents->count()} documenti in corso...");

        $bar = $this->output->createProgressBar($documents->count());
        $bar->start();

        foreach ($documents as $doc) {
            $matched = false;

            foreach ($types as $type) {
                // Controllo Regex sul nome del file
                if (preg_match($type->regex, $doc->name)) {
                    $doc->document_type_id = $type->id;
                    $doc->save();
                    $matched = true;
                    break;  // Trovato il match, passiamo al prossimo documento
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Classificazione completata!');
    }
}
