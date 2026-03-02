<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Rimuovo il vecchio campo enum requester_type
            $table->dropColumn('requester_type');
            
            // Aggiungo i campi polimorfici per requester
            $table->string('requester_type')->nullable()->comment('Tipo del modello requester (principal, agent, regulatory_body, company)');
            $table->string('requester_id')->nullable()->comment('ID del modello requester (supporta UUID e integer)');
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Rimuovo i campi polimorfici
            $table->dropColumn(['requester_type', 'requester_id']);
            
            // Ripristino l'enum originale
            $table->enum('requester_type', ['OAM', 'PRINCIPAL', 'INTERNAL', 'EXTERNAL'])->nullable()->comment("Chi richiede l'audit: Ente Regolatore, Mandante o Auto-controllo interno");
        });
    }
};
