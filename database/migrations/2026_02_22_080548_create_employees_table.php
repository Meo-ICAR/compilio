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
        Schema::create('employees', function (Blueprint $table) {
            $table->comment('Anagrafica dipendenti interni delle società di mediazione.');
            $table->increments('id')->comment('ID univoco dipendente');
            $table->char('company_id', 36)->index('company_id')->comment('Agenzia di appartenenza');
            $table->unsignedInteger('user_id')->nullable()->unique('user_id')->comment("Legame con l'utente di sistema");
            $table->string('name')->nullable()->comment('Nome completo dipendente');
            $table->string('role_title', 100)->nullable()->comment('Qualifica aziendale (es. Responsabile Backoffice)');
            $table->string('cf', 16)->nullable()->comment('Codice Fiscale');
            $table->string('email', 100)->nullable()->comment('Email aziendale dipendente');
            $table->string('phone', 16)->nullable()->comment('Telefono o interno dipendente');
            $table->string('department', 100)->nullable()->comment('Dipartimento (es. Amministrazione, Compliance)');
            $table->string('oam', 100)->nullable()->comment('Codice OAM individuale dipendente');
            $table->string('ivass', 100)->nullable()->comment('Codice IVASS individuale dipendente');
            $table->date('hiring_date')->nullable()->comment('Data di assunzione');
            $table->date('termination_date')->nullable()->comment('Data di fine rapporto');
            $table->unsignedInteger('company_branch_id')->nullable()->index('company_branch_id')->comment('Sede fisica di assegnazione');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
