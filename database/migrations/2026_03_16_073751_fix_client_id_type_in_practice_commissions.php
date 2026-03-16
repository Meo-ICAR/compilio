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
        Schema::table('practice_commissions', function (Blueprint $table) {
            // Cambiamo il tipo in bigint per compatibilità
            $table->bigInteger('client_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_commissions', function (Blueprint $table) {
            // Torniamo al tipo originale
            $table->unsignedBigInteger('client_id')->nullable()->change();
        });
    }
};
