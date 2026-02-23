<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compliance_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->unique(['company_id', 'violation_type']);

            // Chi ha causato l'anomalia? (Può essere null se è un attacco esterno)
            $table->unsignedInteger('user_id')->nullable()->comment('Utente che ha causato la violazione');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // A quale entità è legata? (Polimorfica: può essere un Dossier, un Client, ecc.)
            $table->nullableMorphs('violatable');

            // Dettagli della violazione
            $table->string('violation_type')->comment('Es: accesso_non_autorizzato, kyc_scaduto, forzatura_stato, data_breach');
            $table->enum('severity', ['basso', 'medio', 'alto', 'critico'])->default('medio');
            $table->text('description')->comment("Descrizione dettagliata dell'evento");

            // Campi specifici per GDPR / Data Breach
            $table->integer('affected_subjects_count')->nullable()->comment('Numero approssimativo di clienti/utenti coinvolti');
            $table->text('likely_consequences')->nullable()->comment("Possibili conseguenze per gli interessati (es. furto d'identità, frode finanziaria)");
            $table->dateTime('discovery_date')->nullable()->comment("Data e ora in cui l'azienda ha scoperto la violazione (inizio delle 72h)");

            // Dati tecnici e legali (Fondamentali per il Garante Privacy)
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable()->comment('Browser e dispositivo utilizzato');

            // Checkbox e date legali
            $table->boolean('is_dpa_notified')->default(false)->comment('Il Garante Privacy è stato notificato?');
            $table->dateTime('dpa_notified_at')->nullable();
            $table->text('dpa_not_notified_reason')->nullable()->comment('Se non notificato, motivazione legale (es. rischio improbabile per i diritti)');
            $table->boolean('are_subjects_notified')->default(false)->comment('I clienti coinvolti sono stati avvisati?');

            // Gestione e Risoluzione (L'Admin deve chiudere l'incidente)
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedInteger('resolved_by')->nullable()->comment('Utente che ha risolto la violazione');
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable()->comment('Come è stata sanata la violazione?');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_violations');
    }
};
