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
        Schema::create('practices', function (Blueprint $table) {
            $table->comment('Pratiche di mediazione (Mutui, Cessioni, Prestiti personali) caricate a sistema.');
            $table->increments('id')->comment('ID intero autoincrementante della pratica');
            $table->char('company_id', 36)->index('company_id')->comment('Vincolo multi-tenant: l\'agenzia che gestisce la pratica');
            $table->unsignedInteger('client_id')->index('client_id')->comment('Il cliente richiedente');
            $table->unsignedInteger('principal_id')->index('principal_id');
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('agent_id')->index('agent_id')->comment('L\'agente o collaboratore a cui verranno calcolate le provvigioni');
            $table->string('name')->comment('Codice o nome identificativo (es. Mutuo Acquisto Prima Casa Rossi)');
            $table->string('CRM_code');
            $table->string('principal_code');
            $table->decimal('amount', 12)->comment('Importo del finanziamento/mutuo richiesto o erogato');
            $table->decimal('net', 12)->comment('Netto erogato');
            $table->unsignedInteger('practice_scope_id')->index('practice_scope_id');
            $table->string('status', 50)->default('istruttoria')->comment('Stato: istruttoria, deliberata, erogata, respinta');
            $table->date('perfected_at');
            $table->boolean('is_active');
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
