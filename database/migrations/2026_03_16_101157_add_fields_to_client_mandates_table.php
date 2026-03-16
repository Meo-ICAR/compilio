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
        Schema::table('client_mandates', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->comment('Descrizione');
            $table->text('notes')->nullable()->comment('Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")');
            $table->text('purpose_of_relationship')->nullable()->comment('Es: Acquisto prima casa');
            $table->text('funds_origin')->nullable()->comment('Es: Risparmi, donazione, stipendio');
            $table->tinyInteger('oam_delivered')->default(0)->comment('Foglio informativo consegnato a questo soggetto?');
            $table->enum('role_risk_level', ['basso', 'medio', 'alto'])->nullable()->comment('Livello rischio specifico ruolo nella pratica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_mandates', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'notes',
                'purpose_of_relationship',
                'funds_origin',
                'oam_delivered',
                'role_risk_level'
            ]);
        });
    }
};
