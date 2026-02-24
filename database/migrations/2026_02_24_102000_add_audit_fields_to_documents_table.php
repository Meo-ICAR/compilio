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
        Schema::table('documents', function (Blueprint $table) {
            // --- FOREIGN KEYS ---
            // Nota: verified_by e uploaded_by sono già char(36) e users.id è int unsigned
            // Per compatibilità, non aggiungiamo le foreign keys per ora
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Non faccio nulla per non rompere le funzionalità esistenti
        });
    }
};
