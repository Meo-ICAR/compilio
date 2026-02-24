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
        Schema::create('abis', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID univoco interno');
            $table->string('abi', 5)->unique()->comment('Codice ABI a 5 cifre');
            $table->string('name')->comment('Nome ufficiale (es. AGOS DUCATO S.P.A.)');
            $table->enum('type', ['BANCA', 'INTERMEDIARIO_106', 'IP_IMEL'])->comment('Banca o Finanziaria ex Art. 106 TUB');
            $table->string('capogruppo')->nullable()->comment('Gruppo bancario di appartenenza');
            $table->string('status')->default('OPERATIVO')->comment('OPERATIVO, CANCELLATO, IN_LIQUIDAZIONE');
            $table->date('data_iscrizione')->nullable();
            $table->date('data_cancellazione')->nullable();
            $table->timestamp('created_at')->nullable()->comment('Data creazione record');
            $table->timestamp('updated_at')->nullable()->comment('Data ultimo aggiornamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abis');
    }
};
