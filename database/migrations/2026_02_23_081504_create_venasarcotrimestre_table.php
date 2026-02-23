<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venasarcotrimestre', function (Blueprint $table) {
            $table->comment('Totali ENASARCO trimestrali per produttore');
            $table->id();
            $table->integer('competenza')->unsigned()->nullable()->comment('Anno di competenza');
            $table->integer('trimestre')->nullable()->comment('Numero trimestre (1-4)');
            $table->string('produttore')->nullable()->comment('Ragione sociale del referente');
            $table->enum('enasarco', ['no', 'monomandatario', 'plurimandatario', 'societa'])
                ->default('plurimandatario')
                ->comment('Tipo di mandato ENASARCO');
            $table->decimal('montante', 37, 2)->nullable()->comment('Montante provvigioni');
            $table->decimal('contributo', 47, 8)->nullable()->comment('Contributo ENASARCO');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venasarcotrimestre');
    }
};
