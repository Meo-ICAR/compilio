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
            $table->uuid('id')->primary();

            // Questo crea automaticamente 'documentable_id' e 'documentable_type'
            $table->uuidMorphs('documentable');
            $table->unsignedInteger('document_type_id')->index()->comment('ID del tipo di documento associato')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->default('uploaded');

            $table->date('expires_at')->nullable()->comment('Scadenza documento');

            $table->date('emitted_at')->nullable();
            $table->string('docnumber')->nullable()->comment('Numero documento');

            $table->string('emitted_by')->nullable()->comment('Ente rilascio');
            $table->boolean('is_signed')->default(false)->comment('Indica se il documento deve essere firmato');

            $table->timestamps();
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
