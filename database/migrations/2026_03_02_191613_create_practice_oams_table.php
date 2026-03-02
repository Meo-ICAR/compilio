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
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('practice_id')->nullable();
            $table->unsignedBigInteger('oam_code_id')->nullable();

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

            $table->timestamps();

            $table->index('practice_id');
            $table->index('oam_code_id');

            $table->foreign('practice_id')->references('id')->on('practices');
            $table->foreign('oam_code_id')->references('id')->on('oam_codes');
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
