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
            $table->string('oam_name')->nullable();

            // Commission fields
            $table->decimal('compenso', 10, 2)->nullable();
            $table->decimal('compenso_lavorazione', 10, 2)->nullable();
            $table->decimal('erogato', 10, 2)->nullable();
            $table->decimal('erogato_lavorazione', 10, 2)->nullable();
            $table->decimal('liquidato', 10, 2)->nullable();
            $table->decimal('liquidato_lavorazione', 10, 2)->nullable();
            $table->decimal('compenso_premio', 10, 2)->nullable();
            $table->decimal('compenso_rimborso', 10, 2)->nullable();
            $table->decimal('compenso_assicurazione', 10, 2)->nullable();
            $table->decimal('compenso_cliente', 10, 2)->nullable();
            $table->decimal('storno', 10, 2)->nullable();

            // Provision fields
            $table->decimal('provvigione', 10, 2)->nullable();
            $table->decimal('provvigione_lavorazione', 10, 2)->nullable();
            $table->decimal('provvigione_premio', 10, 2)->nullable();
            $table->decimal('provvigione_rimborso', 10, 2)->nullable();
            $table->decimal('provvigione_assicurazione', 10, 2)->nullable();
            $table->decimal('provvigione_storno', 10, 2)->nullable();

            // Status fields
            $table->boolean('is_active')->default(1)->comment('Campo per escludere manualmente');
            $table->boolean('is_cancel')->default(0);
            $table->boolean('is_perfected')->default(0)->comment('Pratica perfezionata nel periodo');
            $table->boolean('is_conventioned')->default(0)->comment('Pratica convenzionata');
            $table->boolean('is_notconventioned')->default(0)->comment('Pratica non convenzionata');
            $table->boolean('is_notconvenctioned')->default(0)->comment('Pratica convenzionata');
            $table->boolean('is_working')->default(1)->comment('PracticeOam is working boolean');

            // Date fields
            $table->date('inserted_at')->nullable()->comment('Data di inserimento');
            $table->date('invoice_at')->nullable()->comment('Data di fatturazione');
            $table->date('start_date')->nullable()->comment('Data di inizio');
            $table->date('perfected_at')->nullable()->comment('Data di perfezionamento');
            $table->date('end_date')->nullable()->comment('Data di fine');
            $table->date('accepted_at')->nullable()->comment('Data inizio autorizzazione');
            $table->date('canceled_at')->nullable();

            // Other fields
            $table->boolean('is_invoice')->default(0)->comment('Pratica fatturata');

            $table->string('name')->nullable()->comment('Mandanti');
            $table->string('tipo_prodotto')->nullable()->comment('Prodotto');
            $table->integer('mese')->nullable()->comment('Mese');
            $table->char('company_id', 36)->nullable();

            // Indexes
            $table->index('practice_id');
            $table->index('oam_code_id');
            $table->index('company_id');

            // Foreign keys
            $table->foreign('practice_id')->references('id')->on('practices');
            $table->foreign('oam_code_id')->references('id')->on('oam_codes');

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
