<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('functions', function (Blueprint $table) {
            $table->id();

            // Il nuovo campo code univoco
            $table->string('code')->unique();

            $table->enum('macro_area', [
                'Governance',
                'Business / Commerciale',
                'Supporto',
                'Controlli (II Livello)',
                'Controlli (III Livello)',
                'Controlli / Privacy'
            ]);

            $table->enum('name', [
                'Consiglio di Amministrazione / Direzione',
                'Direzione Commerciale',
                'Gestione Rete e Collaboratori',
                'Back Office / Istruttoria Pratiche',
                'Amministrazione e Contabilità',
                'IT e Sicurezza Dati',
                'Marketing e Comunicazione',
                'Gestione Reclami e Controversie',
                'Risorse Umane (HR) e Formazione',
                'Compliance (Conformità)',
                'Risk Management',
                'Antiriciclaggio (AML)',
                'Internal Audit (Revisione Interna)',
                'Data Protection Officer (DPO)'
            ]);

            $table->enum('type', [
                'Strategica',
                'Operativa',
                'Supporto',
                'Controllo'
            ]);

            $table->text('description')->nullable();

            $table->enum('outsourcable_status', ['yes', 'no', 'partial'])->default('no');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('functions');
    }
};
