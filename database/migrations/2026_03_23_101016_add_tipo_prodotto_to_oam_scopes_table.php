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
        Schema::table('oam_scopes', function (Blueprint $table) {
            $table->string('tipo_prodotto')->nullable()->after('description')->comment('Lista di tipi prodotto da PracticeOam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oam_scopes', function (Blueprint $table) {
            $table->dropColumn('tipo_prodotto');
        });
    }
};
