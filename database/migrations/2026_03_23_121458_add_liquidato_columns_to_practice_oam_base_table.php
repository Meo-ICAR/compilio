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
            $table->decimal('liquidato', 10, 2)->nullable()->default(0)->after('O_Provvigione_Rete');
            $table->decimal('liquidato_lavorazione', 10, 2)->nullable()->default(0)->after('liquidato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_oam_base', function (Blueprint $table) {
            $table->dropColumn(['liquidato', 'liquidato_lavorazione']);
        });
    }
};
