<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36)->index();
            $table->string('name');  // Es: "Istruttoria Pratica", "Revisione Annuale KYC"
            $table->string('slug')->unique();
            $table->string('groupcode')->nullable();

            // Gestione Periodicità
            $table
                ->enum('periodicity', ['once', 'monthly', 'quarterly', 'semiannual', 'annual'])
                ->default('once');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign key
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Indexes
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'periodicity']);
            $table->index('groupcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
