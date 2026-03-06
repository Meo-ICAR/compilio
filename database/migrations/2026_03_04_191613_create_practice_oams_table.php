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
        Schema::create('practice_oams', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('practice_id')->nullable();
            $table->unsignedBigInteger('oam_code_id')->nullable();
            $table->string('oam_code')->nullable();

            $table->decimal('compenso', 10, 2)->nullable();
            $table->decimal('compenso_lavorazione', 10, 2)->nullable();
            $table->decimal('compenso_premio', 10, 2)->nullable();
            $table->decimal('compenso_rimborso', 10, 2)->nullable();
            $table->decimal('compenso_assicurazione', 10, 2)->nullable();
            $table->decimal('compenso_cliente', 10, 2)->nullable();

            $table->decimal('storno', 10, 2)->nullable();

            $table->decimal('provvigione', 10, 2)->nullable();
            $table->decimal('provvigione_lavorazione', 10, 2)->nullable();
            $table->decimal('provvigione_premio', 10, 2)->nullable();
            $table->decimal('provvigione_rimborso', 10, 2)->nullable();
            $table->decimal('provvigione_assicurazione', 10, 2)->nullable();
            $table->decimal('provvigione_storno', 10, 2)->nullable();

            $table->boolean('is_active')->default(true)->comment('Campo per escludere manualmente')->nullable();
            $table->boolean('is_perfected')->default(false)->comment('Pratica perfezionata nel periodo')->nullable();
            $table->boolean('is_conventioned')->default(false)->comment('Pratica convenzionata')->nullable();
            $table->boolean('is_notconventioned')->default(false)->comment('Pratica non convenzionata')->nullable();
            $table->date('inserted_at')->nullable()->comment('Data di inserimento');
            $table->date('invoice_at')->nullable()->comment('Data di fatturazione');
            $table->boolean('is_invoice')->default(false)->comment('Pratica fatturata')->nullable();
            $table->decimal('erogato', 10, 2)->nullable();
            $table->decimal('erogato_lavorazione', 10, 2)->nullable();
            $table->date('start_date')->nullable()->comment('Data di inizio');
            $table->date('perfected_at')->nullable()->comment('Data di perfezionamento');
            $table->date('end_date')->nullable()->comment('Data di fine');

            $table->boolean('is_cancel')->default(false)->comment('Pratica stornata')->nullable();
            $table->date('canceled_at')->nullable()->comment('Data di storno');
            $table->boolean('is_notconvenctioned')->default(false)->comment('Pratica convenzionata')->nullable();
            $table->string('name')->nullable()->comment('Mandanti');
            $table->string('tipo_prodotto')->nullable()->comment('Prodotto');
            $table->integer('mese')->nullable()->comment('Mese');

            $table->index('practice_id');
            $table->index('oam_code_id');

            $table->foreign('practice_id')->references('id')->on('practices');
            $table->foreign('oam_code_id')->references('id')->on('oam_codes');
            $table->boolean('is_working')->default(true)->comment('PracticeOam is working boolean')->nullable();
            $table->date('accepted_at')->comment('Data inizio autorizzazione')->nullable();

            $table->char('company_id', 36)->nullable();
            $table->index('company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_oams');
    }
};
