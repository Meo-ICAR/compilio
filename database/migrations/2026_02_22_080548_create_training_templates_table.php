<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_templates', function (Blueprint $table) {
            $table->comment('Catalogo globale: Modelli predefiniti di corsi di formazione.');
            $table->increments('id')->comment('ID univoco template');
            $table->string('name')->comment('Titolo del corso (es. Aggiornamento Professionale OAM 2024)');
            $table->enum('category', ['OAM', 'IVASS', 'GDPR', 'SICUREZZA', 'PRODOTTO', 'SOFT_SKILLS'])->comment('Categoria normativa o tecnica del corso');
            $table->decimal('base_hours', 5)->comment('Numero di ore standard previste per questo corso');
            $table->text('description')->nullable()->comment('Programma del corso e obiettivi formativi');
            $table->boolean('is_mandatory')->nullable()->default(false)->comment('Indica se il corso è obbligatorio per legge');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_templates');
    }
};
