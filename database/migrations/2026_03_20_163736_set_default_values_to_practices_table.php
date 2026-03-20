<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            // Update existing NULL values to 0 first
            DB::statement('UPDATE practices SET net = 0 WHERE net IS NULL');
            DB::statement('UPDATE practices SET amount = 0 WHERE amount IS NULL');
            DB::statement('UPDATE practices SET brokerage_fee = 0 WHERE brokerage_fee IS NULL');

            // Then modify columns to have default 0 and be NOT NULL
            $table->decimal('net', 15, 2)->default(0)->change();
            $table->decimal('amount', 15, 2)->default(0)->change();
            $table->decimal('brokerage_fee', 15, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->decimal('net', 15, 2)->nullable()->default(null)->change();
            $table->decimal('amount', 15, 2)->nullable()->default(null)->change();
            $table->decimal('brokerage_fee', 15, 2)->nullable()->default(null)->change();
        });
    }
};
