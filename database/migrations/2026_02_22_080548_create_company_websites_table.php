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
        Schema::create('company_websites', function (Blueprint $table) {
            $table->comment('Configurazioni dei siti web e portali personalizzati per ogni agenzia.');
            $table->increments('id')->comment('ID univoco del sito');
            $table->foreignId('company_id')->constrained()->index()->comment('Tenant proprietario del sito');
            $table->string('name')->comment('Nome del sito (es. Portale Agenti Roma)');
            $table->string('domain')->unique('domain')->comment('Dominio o sottodominio (es. agenzia-x.mediaconsulence.it)');
            $table->string('type')->nullable()->comment('Tipologia sito (Vetrina, Portale, Landing)');
            $table->unsignedInteger('principal_id')->nullable()->index('principal_id')->comment('Mandante di riferimento per landing dedicate');
            $table->boolean('is_active')->nullable()->default(true)->comment('Stato del sito (online/offline)');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_websites');
    }
};
