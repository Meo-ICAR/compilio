<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('practice_statuses', function (Blueprint $table) {
            $table->comment('Storico cronologico dei cambi di stato della pratica per monitorare i tempi di lavorazione (KPI).');
            $table->increments('id');
            $table->unsignedInteger('practice_id')->comment('La pratica a cui si riferisce il cambio di stato');
            $table->string('status', 50)->comment('Valore dello stato (es. istruttoria, delibera, erogata, annullata)');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->text('notes')->nullable()->comment('Eventuali motivazioni o note interne (es. motivo del respinto)');
            $table->unsignedInteger('changed_by')->comment('L\'utente (backoffice/admin) che ha aggiornato lo stato');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data e ora esatta del cambio stato (Audit Log)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_statuses');
    }
};
