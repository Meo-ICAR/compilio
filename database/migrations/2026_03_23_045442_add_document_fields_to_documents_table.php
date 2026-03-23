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
            if (!Schema::hasColumn('documents', 'document_type')) {
                $table->string('document_type')->nullable();
            }
            if (!Schema::hasColumn('documents', 'collection')) {
                $table->string('collection')->nullable()->comment('Raccolta Spatie');
            }
            if (!Schema::hasColumn('documents', 'is_unique')) {
                $table->boolean('is_unique')->nullable()->default(false)->comment('documento unico nella collection');
            }
            // is_endMonth già esistente, non aggiungere
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'collection', 'is_unique']);
        });
    }
};
