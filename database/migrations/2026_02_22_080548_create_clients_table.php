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
        Schema::create('clients', function (Blueprint $table) {
            $table->comment('Clienti (Richiedenti credito) associati in modo esclusivo a una specifica agenzia (Tenant).');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->char('company_id', 36)->index('company_id')->comment("Vincolo multi-tenant: l'agenzia proprietaria del dato");
            $table->boolean('is_person')->default(true)->comment('Persona fisica (true) o giuridica (false)');
            $table->string('name')->comment('Cognome (se persona fisica) o Ragione Sociale (se giuridica)');
            $table->string('first_name')->nullable()->comment('Nome persona fisica');
            $table->string('tax_code', 16)->nullable()->comment('Codice Fiscale o Partita IVA del cliente');
            $table->string('email')->nullable()->comment('Email di contatto principale');
            $table->string('phone', 50)->nullable()->comment('Recapito telefonico');
            $table->boolean('is_pep')->default(false)->comment('Persona Politicamente Esposta');
            $table->unsignedInteger('client_type_id')->nullable()->index('client_type_id')->comment('Classificazione cliente');
            $table->boolean('is_sanctioned')->default(false)->comment('Presente in liste antiterrorismo/blacklists');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data acquisizione cliente');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica anagrafica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
