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
        Schema::create('practice_statuses', function (Blueprint $table) {
            $table->comment('Stati della  pratica');

            $table->increments('id');
            $table->string('code', 50)->comment('Codice dello stato (es. istruttoria, delibera, erogata, annullata)')->nullable();
            $table->string('name')->nullable()->comment('Descrizione');
            $table->string('ordine', 5)->comment('Ordine stato (es. istruttoria, delibera, erogata, annullata)')->nullable();
            $table->string('color', 20)->comment('Colore dello stato')->nullable();
            $table->boolean('is_rejected')->nullable()->default(false)->comment('Stato respinto');
            $table->boolean('is_working')->nullable()->default(false)->comment('Stato in lavorazione');
            $table->boolean('is_completed')->nullable()->default(false)->comment('Stato completato');
            $table->boolean('is_perfectioned')->nullable()->default(false)->comment('Stato perfezionato');
            $table->timestamp('created_at')->nullable()->comment('Data creazione record');
            $table->timestamp('updated_at')->nullable()->comment('Data ultimo aggiornamento');
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
