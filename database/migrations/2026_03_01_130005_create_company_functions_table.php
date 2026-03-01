<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('company_functions', function (Blueprint $table) {
            $table->id();

            // Relazione con l'Azienda (es. il Mediatore Creditizio)
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();

            // Relazione con la Funzione (es. Compliance, AML, Direzione)
            $table
                ->foreignId('function_id')
                ->constrained('functions')
                ->onDelete('cascade');

            // Referente Interno (Dipendente/Esponente aziendale delegato al controllo)
            $table
                ->unsignedInteger('internal_employee_id')
                ->nullable()
                ->comment('ID del dipendente referente interno');

            // Foreign key verso employees
            $table
                ->foreign('internal_employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');

            // Referente Esterno / Outsourcer (usando la tua tabella clients)
            $table
                ->unsignedInteger('external_client_id')
                ->nullable()
                ->comment('ID del cliente referente esterno');

            // Foreign key verso clients
            $table
                ->foreign('external_client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('set null');
            $table->boolean('is_privacy')->default(true);
            // Dettagli operativi dell'assegnazione
            $table->boolean('is_outsourced')->default(false);
            $table->string('report_frequency')->nullable();  // Es. Mensile, Trimestrale
            $table->date('contract_expiry_date')->nullable();  // Scadenza contratto outsourcer
            $table->text('notes')->nullable();

            // Indice univoco: un'azienda non può avere due volte la stessa funzione assegnata
            $table->unique(['company_id', 'function_id']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_function');
    }
};
