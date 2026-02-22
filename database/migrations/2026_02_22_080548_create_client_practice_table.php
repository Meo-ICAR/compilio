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
        Schema::create('client_practice', function (Blueprint $table) {
            $table->comment('Tabella di legame tra Clienti e Pratiche. Gestisce chi sono gli intestatari e chi i garanti per ogni pratica.');
            $table->increments('id')->comment('ID univoco del legame');
            $table->unsignedInteger('practice_id')->comment('Riferimento alla pratica');
            $table->unsignedInteger('client_id')->index('client_id')->comment('Riferimento al cliente coinvolto');
            $table->enum('role', ['intestatario', 'cointestatario', 'garante', 'terzo_datore'])->default('intestatario')->comment('Ruolo legale del cliente nella pratica: Intestatario principale, Co-intestatario, Garante o Terzo Datore di ipoteca');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->text('notes')->nullable()->comment('Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")');
            $table->char('company_id', 36)->nullable()->index('company_id')->comment('Tenant di riferimento del legame');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di associazione del cliente alla pratica');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['practice_id', 'client_id'], 'unique_client_practice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_practice');
    }
};
