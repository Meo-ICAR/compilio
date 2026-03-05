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
        Schema::create('rui', function (Blueprint $table) {
            $table->comment('Tabella dati RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('oss')->comment('Codice OSS')->nullable();
            $table->boolean('inoperativo')->default(false)->comment('Stato inoperativo')->nullable();
            $table->date('data_inizio_inoperativita')->comment('Data inizio inoperatività')->nullable();
            $table->string('numero_iscrizione_rui', 50)->comment('Numero iscrizione RUI')->nullable();
            $table->date('data_iscrizione')->comment('Data iscrizione RUI')->nullable();
            $table->string('cognome_nome', 255)->comment('Cognome e Nome')->nullable();
            $table->string('stato')->comment('Stato')->nullable();
            $table->string('comune_nascita', 100)->comment('Comune di nascita')->nullable();
            $table->date('data_nascita')->comment('Data di nascita')->nullable();
            $table->string('ragione_sociale', 255)->comment('Ragione sociale')->nullable();
            $table->string('provincia_nascita', 50)->comment('Provincia di nascita')->nullable();
            $table->string('titolo_individuale_sez_a', 100)->comment('Titolo individuale Sezione A')->nullable();
            $table->string('attivita_esercitata_sez_a', 100)->comment('Attività esercitata Sezione A')->nullable();
            $table->string('titolo_individuale_sez_b', 100)->comment('Titolo individuale Sezione B')->nullable();
            $table->string('attivita_esercitata_sez_b', 100)->comment('Attività esercitata Sezione B')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('rui_section_id')->comment('ID sezione RUI')->nullable();
            $table->foreign('rui_section_id')->references('id')->on('rui_sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui');
    }
};
