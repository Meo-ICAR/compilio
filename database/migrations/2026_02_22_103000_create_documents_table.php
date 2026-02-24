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
            $table->char('id', 36)->primary()->comment('UUID del documento');
            $table->foreignId('company_id')->constrained();

            // Campi polymorphic per documentable
            $table->string('documentable_type', 255)->comment('Tipo di modello associato (es. Client, Employee, Practice)');
            $table->char('documentable_id', 36)->comment('ID del modello associato');

            $table->unsignedInteger('document_type_id')->nullable()->comment('ID del tipo di documento associato');
            $table->string('name')->nullable()->comment('Nome del documento');
            $table->string('status')->default('uploaded')->comment('Stato del documento');
            $table->boolean('is_template')->default(false)->comment('Indica se forniamo noi il documento');
            $table->date('expires_at')->nullable()->comment('Scadenza documento');
            $table->date('emitted_at')->nullable()->comment('Data emissione documento');
            $table->string('docnumber')->nullable()->comment('Numero documento');

            // Campi audit aggiunti
            $table->text('rejection_note')->nullable()->comment('Motivazione in caso di documento rifiutato');
            $table->timestamp('verified_at')->nullable()->comment('Data e ora della verifica');
            $table->char('verified_by', 36)->nullable()->comment("ID dell'utente/admin che ha effettuato la verifica");
            $table->char('uploaded_by', 36)->nullable()->comment("ID dell'utente/admin che ha caricato il documento");

            $table->timestamps();

            // Indici
            $table->index(['documentable_type', 'documentable_id']);

            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
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
