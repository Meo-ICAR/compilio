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
        Schema::create('training_records', function (Blueprint $table) {
            $table->comment('Registro presenze e certificazioni: traccia la formazione di agenti e dipendenti per scopi normativi.');
            $table->increments('id')->comment('ID record partecipazione');
            $table->unsignedInteger('training_session_id')->comment('La sessione seguita');
            $table->unsignedInteger('employee_id')->nullable()->index('employee_id')->comment('Dipendente che ha partecipato alla formazione');
            $table->unsignedInteger('agent_id')->nullable()->index('agent_id')->comment('Agente che ha partecipato alla formazione');
            $table->enum('status', ['ISCRITTO', 'FREQUENTANTE', 'COMPLETATO', 'NON_SUPERATO'])->nullable()->default('ISCRITTO');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->decimal('hours_attended', 5)->nullable()->default(0)->comment('Ore effettivamente frequentate dal singolo utente');
            $table->string('score', 50)->nullable()->comment('Esito test finale (es. 28/30 o Idoneo)');
            $table->date('completion_date')->nullable()->comment('Data esatta di conseguimento titolo');
            $table->string('certificate_path')->nullable()->comment("Link al PDF dell'attestato (se salvato fuori da Media Library)");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data creazione record');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data ultimo aggiornamento record');

            $table->unique(['training_session_id', 'employee_id'], 'unique_employee_session');
            $table->unique(['training_session_id', 'agent_id'], 'unique_agent_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
