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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            // Questo crea automaticamente 'documentable_id' e 'documentable_type'
            $table->morphs('documentable');
            $table->unsignedInteger('document_type_id')->index()->comment('ID del tipo di documento associato')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->default('uploaded');
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
