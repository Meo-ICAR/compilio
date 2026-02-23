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
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // A quale entità è legata? (Polimorfica: può essere un Dossier, un Client, ecc.)
            $table->nullableMorphs('violatable');

            // Dettagli della violazione
            $table->string('violation_type')->comment('Es: accesso_non_autorizzato, kyc_scaduto, forzatura_stato, data_breach');
            $table->enum('severity', ['basso', 'medio', 'alto', 'critico'])->default('medio');
            $table->text('description')->comment("Descrizione dettagliata dell'evento");

            // Dati tecnici e legali (Fondamentali per il Garante Privacy)
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable()->comment('Browser e dispositivo utilizzato');

            // Gestione e Risoluzione (L'Admin deve chiudere l'incidente)
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
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
