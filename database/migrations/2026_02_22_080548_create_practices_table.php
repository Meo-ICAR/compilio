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
        Schema::create('practices', function (Blueprint $table) {
            $table->comment('Pratiche di mediazione (Mutui, Cessioni, Prestiti personali) caricate a sistema.');
            $table->increments('id')->comment('ID intero autoincrementante della pratica');
            $table->char('company_id', 36)->index('company_id')->comment("Vincolo multi-tenant: l'agenzia che gestisce la pratica");
            $table->unsignedInteger('principal_id')->index('principal_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->unsignedInteger('agent_id')->index('agent_id')->comment("L'agente o collaboratore a cui verranno calcolate le provvigioni")->nullable();
            $table->string('name')->comment('Codice o nome identificativo (es. Mutuo Acquisto Prima Casa Rossi)');
            $table->string('CRM_code')->nullable();
            $table->string('principal_code')->nullable();
            $table->decimal('amount', 12)->comment('Importo del finanziamento/mutuo richiesto o erogato')->nullable();
            $table->decimal('net', 12)->comment('Netto erogato')->nullable();
            $table->unsignedInteger('practice_scope_id')->index('practice_scope_id')->nullable();
            $table->string('status', 50)->default('istruttoria')->comment('Stato: istruttoria, deliberata, erogata, respinta')->nullable();
            $table->date('perfected_at')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data caricamento pratica');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data ultimo cambio stato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
