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
        Schema::create('document_types', function (Blueprint $table) {
            $table->comment("Tabella di lookup globale (Senza Tenant): Tipologie di documenti riconosciuti per l'Adeguata Verifica.");
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->string('code')->nullable()->comment('Codice documento');
            $table->boolean('is_person')->default(true)->comment('Documento inerente Persona o azienda');
            $table->boolean('is_signed')->default(false)->comment('Indica se il documento deve essere firmato');
            $table->boolean('is_monitored')->default(false)->comment('Indica se la scadenza documento deve essere monitorata nel tempo');
            $table->integer('duration')->nullable()->comment('Validità dal rilascio in giorni');
            $table->string('emitted_by')->nullable()->comment('Ente di rilascio');
            $table->boolean('is_sensible')->default(false)->comment('Indica se contiene dati sensibili');
            $table->boolean('is_template')->default(false)->comment('Indica se forniamo noi il documento');
            $table->boolean('is_stored')->default(false)->comment('Indica se il documento deve avere conservazione sostitutiva');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
