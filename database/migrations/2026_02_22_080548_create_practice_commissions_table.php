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
        Schema::create('practice_commissions', function (Blueprint $table) {
            $table->comment('Singole righe provvigionali maturate dalle pratiche. Vengono raggruppate nel proforma mensile.');
            $table->increments('id');
            $table->char('company_id', 36)->index('company_id')->comment('Tenant dell\'agenzia');
            $table->unsignedInteger('practice_id')->index('practice_id')->comment('La pratica che ha generato la provvigione');
            $table->unsignedInteger('proforma_id')->nullable()->index('proforma_id')->comment('Il proforma in cui questa provvigione è stata liquidata (NULL se non ancora liquidata)');
            $table->unsignedInteger('agent_id')->nullable()->index('agent_id')->comment('L\'agente beneficiario');
            $table->unsignedInteger('principal_id')->nullable()->comment('Mandante');
            $table->decimal('amount', 10)->nullable()->comment('Importo provvigionale lordo per questa singola pratica');
            $table->string('description')->nullable()->comment('Dettaglio (es. Bonus extra o Provvigione base)');
            $table->date('perfected_at')->nullable();
            $table->boolean('is_coordination')->nullable()->comment('Compenso coordinamento');
            $table->date('cancellation_at')->nullable();
            $table->string('invoice_number', 30)->nullable();
            $table->date('invoice_at')->nullable();
            $table->date('paided_at')->nullable();
            $table->boolean('is_storno')->nullable();
            $table->boolean('is_enasarco')->nullable();
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
