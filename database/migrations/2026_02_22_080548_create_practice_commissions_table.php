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
        Schema::create('practice_commissions', function (Blueprint $table) {
            $table->comment('Singole righe provvigionali maturate dalle pratiche. Vengono raggruppate nel proforma mensile.');
            $table->increments('id');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
            $table->unsignedInteger('practice_id')->comment('La pratica che ha generato la provvigione');
            $table->unsignedInteger('proforma_id')->nullable()->comment('Il proforma in cui questa provvigione è stata liquidata (NULL se non ancora liquidata)');
            $table->unsignedInteger('agent_id')->nullable()->comment('Agente beneficiario');
            $table->unsignedInteger('principal_id')->nullable()->comment('Mandante');
            $table->string('CRM_code')->nullable()->comment('Codice CRM');
            $table->date('inserted_at')->nullable();
            $table->boolean('is_enasarco')->nullable()->default(true);
            $table->boolean('is_payment')->nullable();
            $table->boolean('is_client')->nullable();
            $table->boolean('is_coordination')->nullable()->comment('Compenso coordinamento')->default(false);
            $table->string('tipo')->nullable()->comment('Tipo di provvigione');
            $table->string('name')->nullable()->comment('Provvigione');
            $table->decimal('amount', 10)->nullable()->comment('Importo provvigionale lordo per questa singola pratica');
            $table->string('description')->nullable()->comment('Dettaglio (es. Bonus extra o Provvigione base)');
            $table->string('status_payment')->nullable()->comment('Stato pagamento');
            $table->date('status_at')->nullable();
            $table->date('perfected_at')->nullable();

            $table->date('cancellation_at')->nullable();
            $table->string('invoice_number', 30)->nullable();
            $table->date('invoice_at')->nullable();
            $table->date('paided_at')->nullable();

            $table->boolean('is_storno')->nullable();
            $table->date('storned_at')->nullable();
            $table->decimal('storno_amount', 10)->nullable()->comment('Importo provvigionale stornato');

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_commissions');
    }
};
