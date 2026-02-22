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
