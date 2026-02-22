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
        Schema::table('audits', function (Blueprint $table) {
            $table->foreign(['company_id'], 'audits_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['principal_id'], 'audits_ibfk_2')->references(['id'])->on('principals')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['agent_id'], 'audits_ibfk_3')->references(['id'])->on('agents')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeign('audits_ibfk_1');
            $table->dropForeign('audits_ibfk_2');
            $table->dropForeign('audits_ibfk_3');
        });
    }
};
