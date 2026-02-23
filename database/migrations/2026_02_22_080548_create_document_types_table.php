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
            $table->string('name')->comment('Descrizione');
            $table->boolean('is_person')->default(true)->comment('Persona fisica (true) o giuridica (false)');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di inserimento a sistema');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica');
            $table->boolean('is_signed')->default(false)->comment('Indica se il documento deve essere firmato');

            $table->integer('duration')->nullable()->comment('Validità dal rilascio in giorni');
            $table->string('emitted_by')->nullable()->comment('Ente di rilascio');
            $table->boolean('is_sensible')->default(false)->comment('Indica se contiene dati sensibili');
            $table->boolean('is_template')->default(false)->comment('Indica se forniamo noi il documento');
            $table->boolean('is_stored')->default(false)->comment('Indica se il documento deve avere conservazione sostitutiva');
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
