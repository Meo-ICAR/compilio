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
        Schema::table('rui_collaboratori', function (Blueprint $table) {
            $table->string('intermediario')->nullable()->after('qualifica_rapporto');
            $table->string('collaboratore')->nullable()->after('intermediario');
            $table->string('dipendente')->nullable()->after('collaboratore');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rui_collaboratori', function (Blueprint $table) {
            $table->dropColumn(['intermediario', 'collaboratore', 'dipendente']);
        });
    }
};
