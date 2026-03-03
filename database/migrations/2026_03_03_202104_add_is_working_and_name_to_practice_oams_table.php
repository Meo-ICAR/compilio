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
        Schema::table('practice_oams', function (Blueprint $table) {
            $table->boolean('is_working')->default(true)->comment('PracticeOam is working boolean');
            $table->string('name')->nullable()->comment('Mandanti');
            $table->string('tipo_prodotto')->nullable()->comment('Prodotto');
            $table->integer('mese')->nullable()->comment('Mese');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_oams', function (Blueprint $table) {
            $table->dropColumn('is_working');
            $table->dropColumn('name');
        });
    }
};
