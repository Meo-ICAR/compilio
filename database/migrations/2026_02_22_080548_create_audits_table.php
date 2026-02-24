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
        Schema::create('audits', function (Blueprint $table) {
            $table->comment('Sessioni di Audit richieste da OAM, Mandanti o effettuate internamente.');
            $table->increments('id')->comment('ID univoco audit');
            $table->foreignId('company_id')->constrained();

            $table->enum('requester_type', ['OAM', 'PRINCIPAL', 'INTERNAL', 'EXTERNAL'])->comment("Chi richiede l'audit: Ente Regolatore, Mandante o Auto-controllo interno");

            $table->string('title')->comment("Titolo dell'ispezione (es. Audit Semestrale Trasparenza 2026)");
            $table->string('emails')->default('')->comment('Lista email per notifiche esiti audit');
            $table->string('reference_period', 100)->nullable()->comment('Periodo oggetto di analisi (es. Q1-Q2 2025)');
            $table->date('start_date')->comment('Data inizio ispezione');
            $table->date('end_date')->nullable()->comment('Data chiusura ispezione');
            $table->enum('status', ['PROGRAMMATO', 'IN_CORSO', 'COMPLETATO', 'ARCHIVIATO'])->nullable()->default('PROGRAMMATO');
            $table->string('overall_score', 50)->nullable()->comment('Valutazione sintetica finale (es. Conforme, Conforme con rilievi, Non Conforme)');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
