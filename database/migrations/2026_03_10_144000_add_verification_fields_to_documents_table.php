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
        Schema::table('documents', function (Blueprint $table) {
            // Campi per verifica documento
            $table->timestamp('verified_at')->nullable()->comment('Data e ora verifica documento');
            $table->foreignId('verified_by')->nullable()->comment('ID utente che ha verificato')->constrained('users')->onDelete('set null');

            // Campi aggiuntivi per gestione completa
            $table->string('docnumber')->nullable()->comment('Numero protocollo documento');
            $table->string('emitted_by')->nullable()->comment('Ente o autorità che ha rilasciato il documento');
            $table->date('emitted_at')->nullable()->comment('Data di rilascio/emissione documento');
            $table->date('expires_at')->nullable()->comment('Data di scadenza documento');

            // Note di rifiuto
            $table->text('rejection_note')->nullable()->comment('Note motivazione rifiuto documento');

            // Index per performance
            $table->index(['verified_at', 'verified_by']);
            $table->index(['expires_at']);
            $table->index(['emitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verified_at', 'verified_by', 'docnumber', 'emitted_by', 'emitted_at', 'expires_at', 'rejection_note']);
        });
    }
};
