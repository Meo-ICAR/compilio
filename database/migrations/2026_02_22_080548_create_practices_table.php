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
            $table->unsignedInteger('principal_id')->index('principal_id')->nullable()->comment('Mandante (banca)');

            $table->unsignedInteger('agent_id')->index('agent_id')->nullable()->comment('Agente o collaboratore per provvigioni');
            $table->string('name')->comment('Codice o nome identificativo (es. Mutuo Acquisto Prima Casa Rossi)');

            $table->string('CRM_code')->nullable()->comment('Codice CRM interno');
            $table->string('principal_code')->nullable()->comment('Codice mandante');
            $table->decimal('amount', 12)->nullable()->comment('Importo del finanziamento/mutuo richiesto o erogato');
            $table->decimal('net', 12)->nullable()->comment('Netto erogato');
            $table->decimal('brokerage_fee', 10, 2)->nullable()->comment('Provvigione pattuita');
            $table->unsignedInteger('practice_scope_id')->index('practice_scope_id')->nullable()->comment('Ambito della pratica');
            // Lo stato governato da Spatie Model States
            $table->string('status', 50)->default('istruttoria')->comment('Stato: istruttoria, deliberata, erogata, respinta');
            $table->date('perfected_at')->nullable()->comment('Data perfezionamento pratica');

            $table->boolean('is_active')->default(true)->comment('Pratica attiva/inattiva');
            $table->timestamps();
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
