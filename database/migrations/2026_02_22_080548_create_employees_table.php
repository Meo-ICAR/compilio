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
            $table->foreignId('company_id')->constrained()->index()->comment('Agenzia di appartenenza');
            $table->unsignedInteger('user_id')->nullable()->unique()->comment("Legame con l'utente di sistema");
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
            $table->unsignedInteger('company_branch_id')->nullable()->comment('Sede fisica di assegnazione');

            $table->string('privacy_role')->nullable()->comment('Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)');
            $table->text('purpose')->nullable()->comment('Finalità del trattamento');
            $table->text('data_subjects')->nullable()->comment('Categorie di Interessati');
            $table->text('data_categories')->nullable()->comment('Categorie di Dati Trattati');
            $table->string('retention_period')->nullable()->comment('Tempi di Conservazione (Data Retention)');
            $table->string('extra_eu_transfer')->nullable()->comment('Trasferimento Extra-UE');
            $table->text('security_measures')->nullable()->comment('Misure di Sicurezza');
            $table->string('privacy_data')->nullable()->comment('Altri Dati Privacy');

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
