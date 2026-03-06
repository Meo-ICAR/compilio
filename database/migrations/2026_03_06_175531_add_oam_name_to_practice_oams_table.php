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
        Schema::table('practice_oams', function (Blueprint $table) {
            $table->string('oam_name')->nullable()->comment('Codice OAM prodotto');
            $table->string('principal_name')->nullable()->comment('Nome intermediario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_oams', function (Blueprint $table) {
            $table->dropColumn('oam_name');
        });
    }
};
