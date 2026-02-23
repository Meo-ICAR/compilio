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
        Schema::create('client_relations', function (Blueprint $table) {
            $table->id();

            // La società (persona giuridica)
            // foreignId crea un BIGINT UNSIGNED compatibile con l'ID di default di Laravel
            // Deve essere unsignedInteger per matchare clients.id
            $table->unsignedInteger('company_id');
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('clients')
                ->cascadeOnDelete();

            // Deve essere unsignedInteger per matchare clients.id
            $table->unsignedInteger('client_id');
            $table
                ->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->cascadeOnDelete();
            // Il ruolo (socio, amministratore, legale rappresentante)
            $table->decimal('shares_percentage', 5, 2)->nullable();  // Opzionale per le quote
            $table->boolean('is_titolare')->default(false);
            $table->unsignedInteger('client_type_id')->nullable();
            $table->foreign('client_type_id')->references('id')->on('client_types')->cascadeOnDelete();
            $table->date('data_inizio_ruolo')->nullable();
            $table->date('data_fine_ruolo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_relations');
    }
};
