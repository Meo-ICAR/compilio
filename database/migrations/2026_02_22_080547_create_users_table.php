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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('Utenti del sistema: SuperAdmin, Titolari, Agenti e Backoffice.');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->char('company_id', 36)->nullable()->index('company_id')->comment('UUID del Tenant di appartenenza (NULL solo per i Super Admin globali)');
            $table->string('name')->comment('Nome e Cognome dell\'utente');
            $table->string('email')->unique('email')->comment('Email usata per il login');
            $table->string('password')->comment('Password hashata tramite bcrypt/argon2');
            $table->rememberToken()->comment('Token per la sessione "Ricordami"');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data registrazione utente');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultimo aggiornamento profilo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
