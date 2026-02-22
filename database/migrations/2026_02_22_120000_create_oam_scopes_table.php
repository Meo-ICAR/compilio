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
        Schema::create('oam_scopes', function (Blueprint $table) {
            $table->comment('Tabella di lookup globale (Senza Tenant): Ambiti operativi OAM.');
            $table->id()->comment('ID autoincrementante');
            $table->string('code')->unique()->comment('Codice ambito OAM');
            $table->string('name')->comment('Descrizione ambito operativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oam_scopes');
    }
};
