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
        Schema::create('comunes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codice_regione', 3)->index();
            $table->string('codice_unita_territoriale', 6);
            $table->string('codice_provincia_storico', 3)->index();
            $table->string('progressivo_comune', 3);
            $table->string('codice_comune_alfanumerico', 6);
            $table->string('denominazione');
            $table->string('denominazione_italiano');
            $table->string('denominazione_altra_lingua')->nullable();
            $table->string('codice_ripartizione_geografica', 1);
            $table->string('ripartizione_geografica');
            $table->string('denominazione_regione')->index();
            $table->string('denominazione_unita_territoriale');
            $table->string('tipologia_unita_territoriale');
            $table->boolean('capoluogo_provincia')->default(false);
            $table->string('sigla_automobilistica', 2)->index();
            $table->string('codice_comune_numerico', 6);
            $table->string('codice_comune_110_province', 6);
            $table->string('codice_comune_107_province', 6);
            $table->string('codice_comune_103_province', 6);
            $table->string('codice_catastale', 4);
            $table->string('codice_nuts1_2021', 3);
            $table->string('codice_nuts2_2021', 6);
            $table->string('codice_nuts3_2021', 6);
            $table->string('codice_nuts1_2024', 3);
            $table->string('codice_nuts2_2024', 6);
            $table->string('codice_nuts3_2024', 6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunes');
    }
};
