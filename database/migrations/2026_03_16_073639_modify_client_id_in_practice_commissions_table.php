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
            // Per ora solo assicuriamoci che la colonna sia del tipo giusto
            // Il foreign key verrà aggiunto manualmente dopo
            $table->unsignedBigInteger('client_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_commissions', function (Blueprint $table) {
            // Non facciamo nulla nel down per ora
        });
    }
};
