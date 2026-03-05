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
        Schema::create('rui_collaboratori', function (Blueprint $table) {
            $table->comment('Tabella collaboratori RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('oss')->comment('Codice OSS')->nullable();
            $table->string('livello', 10)->comment('Livello collaboratore')->nullable();
            $table->string('num_iscr_intermediario', 50)->comment('Numero iscrizione intermediario')->nullable();
            $table->string('num_iscr_collaboratori_i_liv', 50)->comment('Numero iscrizione collaboratori I livello')->nullable();
            $table->string('num_iscr_collaboratori_ii_liv', 50)->comment('Numero iscrizione collaboratori II livello')->nullable();
            $table->string('qualifica_rapporto', 255)->comment('Qualifica rapporto')->nullable();
            $table->timestamps();
            
            $table->index('num_iscr_intermediario');
            $table->index('num_iscr_collaboratori_i_liv');
            $table->index('num_iscr_collaboratori_ii_liv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_collaboratori');
    }
};
