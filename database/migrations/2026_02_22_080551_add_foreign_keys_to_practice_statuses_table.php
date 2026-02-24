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
        Schema::table('practice_statuses', function (Blueprint $table) {
            $table->foreign(['practice_id'], 'practice_statuses_ibfk_1')->references(['id'])->on('practices')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_statuses', function (Blueprint $table) {
            $table->dropForeign('practice_statuses_ibfk_1');
            $table->dropForeign('practice_statuses_ibfk_2');
        });
    }
};
