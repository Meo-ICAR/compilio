<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->foreign(['company_id'], 'training_sessions_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['training_template_id'], 'training_sessions_ibfk_2')->references(['id'])->on('training_templates')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropForeign('training_sessions_ibfk_1');
            $table->dropForeign('training_sessions_ibfk_2');
        });
    }
};
