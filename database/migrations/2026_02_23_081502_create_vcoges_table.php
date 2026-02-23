<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vcoge', function (Blueprint $table) {
            $table->comment('Riepilogo mensile entrate e uscite');
            $table->tinyIncrements('id');
            $table->string('mese', 7)->nullable()->comment('Mese (formato YYYY-MM)');
            $table->decimal('entrata', 38, 2)->nullable()->comment('Totale entrate');
            $table->decimal('uscita', 38, 2)->nullable()->comment('Totale uscite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vcoge');
    }
};
