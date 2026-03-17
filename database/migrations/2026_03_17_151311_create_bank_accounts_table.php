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
        // Check if table exists first

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36)->nullable();
            // Polimorfismo
            $table->morphs('bankable');

            // Dati Bancari Standard
            $table->string('iban')->unique();
            $table->string('bank_name')->nullable();
            $table->string('bic_swift', 11)->nullable();

            // Campi Richiesti
            $table->date('opened_at')->nullable()->comment('Data di accensione');
            $table->boolean('is_dedicated')->default(false)->comment('Conto dedicato a commessa/appalto');

            // Integrazione Sistemi Automatici (Open Banking)
            $table->string('provider_account_id')->nullable()->unique()->comment('ID del conto nel sistema API (es. Fabrick)');
            $table->decimal('balance', 15, 2)->default(0.0);
            $table->dateTime('last_synced_at')->nullable()->comment('Ultima sincronizzazione estratti conto');
            $table->timestamps();

            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
