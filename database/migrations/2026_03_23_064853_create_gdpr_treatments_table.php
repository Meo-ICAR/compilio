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
        Schema::create('gdpr_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('controller_id')->constrained('gdpr_controllers')->cascadeOnDelete();

            $table->string('title');  // Nome del trattamento
            $table->text('purposes');  // Finalità
            $table->string('legal_basis');  // Base giuridica (es: Consenso, Obbligo Legale)

            // Conservazione
            $table->string('retention_period');
            $table->text('retention_criteria')->nullable();

            // Misure di Sicurezza (JSON per Filament KeyValue o CheckboxList)
            $table->json('security_measures')->nullable();

            $table->boolean('has_dpia')->default(false);
            $table->date('last_review_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_treatments');
    }
};
