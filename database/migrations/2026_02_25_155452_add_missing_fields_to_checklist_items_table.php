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
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->integer('ordine')->default(0)->comment('Ordine di visualizzazione');
            $table->tinyInteger('n_documents')->default(0)->comment('Numero di documenti richiesti (0=nessuno, 1=esatto, 99=multipli)');
            $table->index('ordine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropIndex(['ordine']);
            $table->dropColumn(['ordine', 'n_documents']);
        });
    }
};
