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
        Schema::table('activity_log', function (Blueprint $table) {
            // Aggiungo prima la colonna company_id
            $table->char('company_id', 36)->nullable()->after('batch_uuid')->comment('ID company per multi-tenant');

            // Poi aggiungo la foreign key
            $table->foreign(['company_id'], 'fk_activity_log_company')->references(['id'])->on('companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropForeign('fk_activity_log_company');
            $table->dropColumn('company_id');
        });
    }
};
