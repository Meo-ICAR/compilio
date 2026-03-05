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
        Schema::create('rui_intermediaris', function (Blueprint $table) {
            $table->comment('Tabella intermediari RUI');
            $table->id()->comment('ID autoincrementante');
            $table->string('oss')->comment('Codice OSS')->nullable();
            $table->string('matricola')->comment('Matricola intermediario')->nullable();
            $table->string('codice_compagnia')->comment('Codice compagnia')->nullable();
            $table->string('ragione_sociale')->comment('Ragione sociale')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_intermediaris');
    }
};
