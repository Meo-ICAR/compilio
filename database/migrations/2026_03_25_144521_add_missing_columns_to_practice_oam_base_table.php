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
        Schema::table('practice_oam_base', function (Blueprint $table) {
            $table->decimal('L_Assicurative', 15, 2)->nullable(true)->default(0)->after('K_Provvigione_Istituto_Lavorazione');
            $table->decimal('P_Provvig_Assicurative', 15, 2)->nullable(true)->default(0)->after('O_Provvigione_Rete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_oam_base', function (Blueprint $table) {
            $table->dropColumn(['L_Assicurative', 'P_Provvig_Assicurative']);
        });
    }
};
