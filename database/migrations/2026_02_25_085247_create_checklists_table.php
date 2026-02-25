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
        Schema::create('checklists', function (Blueprint $table) {
            $table->comment('Checklist per workflow con domande e allegati');
            $table->id();
            $table->char('company_id', 36)->nullable()->comment('Agenzia proprietaria (multi-tenant)');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('name')->comment('Nome della checklist')->nullable();
            $table->string('code')->comment('Codice della checklist')->nullable();
            $table->enum('type', ['loan_management', 'audit'])->comment('Tipo di checklist')->nullable();
            $table->text('description')->nullable()->comment('Descrizione della checklist');
            $table->unsignedInteger('principal_id')->nullable()->comment('Principal specifico (se applicabile)');
            $table->boolean('is_practice')->default(false)->comment('Se riferisce a pratiche');
            $table->boolean('is_audit')->default(false)->comment('Se per audit/compliance');
            $table->timestamps();

            // Indici
            $table->index(['company_id', 'type']);
            $table->index('principal_id');

            // Foreign keys

            $table->foreign('principal_id')->references('id')->on('principals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
