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
        // Collegamento Trattamento <-> Interessati
        Schema::create('gdpr_treatment_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained('gdpr_treatments')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('gdpr_subjects')->cascadeOnDelete();
        });

        // Collegamento Trattamento <-> Categorie Dati
        Schema::create('gdpr_treatment_data_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained('gdpr_treatments')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('gdpr_data_categories')->cascadeOnDelete();
        });

        // Collegamento Trattamento <-> Destinatari
        Schema::create('gdpr_treatment_recipient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained('gdpr_treatments')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('gdpr_recipients')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_treatment_recipient');
        Schema::dropIfExists('gdpr_treatment_data_category');
        Schema::dropIfExists('gdpr_treatment_subject');
    }
};
