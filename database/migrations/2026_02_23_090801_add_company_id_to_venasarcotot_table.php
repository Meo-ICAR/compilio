<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venasarcotot', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->comment('ID della company (tenant)');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('venasarcotot', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
