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
        Schema::create('gdpr_controllers', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);  // UUID per compatibilità con tabella companies
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('vat_number')->nullable();  // Partita IVA
            $table->string('representative_name')->nullable();  // Legale rappresentante
            $table->string('dpo_name')->nullable();  // Data Protection Officer
            $table->string('dpo_email')->nullable();  // Email DPO
            $table->timestamp('version_at')->nullable()->default(now());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_controllers');
    }
};
