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
        Schema::create('principal_scopes', function (Blueprint $table) {
            $table->comment('Tabella pivot: definisce quali comparti operativi sono autorizzati per ogni singolo mandato.');
            $table->unsignedInteger('principal_id')->index('principal_id')->comment('Riferimento al mandato');
            $table->unsignedInteger('practice_scope_id')->index('practice_scope_id')->comment('Riferimento all\'ambito (es. Cessione del Quinto, Mutui)');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principal_scopes');
    }
};
