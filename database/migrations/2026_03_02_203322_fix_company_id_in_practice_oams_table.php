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
            $table->dropColumn('company_id');
        });

        Schema::table('practice_oams', function (Blueprint $table) {
            $table->char('company_id', 36)->nullable()->after('id');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_oams', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });

        Schema::table('practice_oams', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->nullable()->after('id');
            $table->index('company_id');
        });
    }
};
