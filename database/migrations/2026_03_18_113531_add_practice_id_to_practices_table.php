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
        Schema::table('practices', function (Blueprint $table) {
            // Modifica il tipo del campo practice_id per renderlo compatibile
            $table->dropColumn('practice_id');
        });

        Schema::table('practices', function (Blueprint $table) {
            $table->unsignedInteger('practice_id')->nullable()->after('id');
            $table->foreign('practice_id')->references('id')->on('practices')->onDelete('set null');
            $table->index('practice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign(['practice_id']);
            $table->dropIndex(['practice_id']);
            $table->dropColumn('practice_id');
        });
    }
};
