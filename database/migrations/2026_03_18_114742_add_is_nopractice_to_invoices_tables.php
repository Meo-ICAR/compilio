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
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->boolean('is_nopractice')->default(false)->after('corrected');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->boolean('is_nopractice')->default(false)->after('corrected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('is_nopractice');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('is_nopractice');
        });
    }
};
