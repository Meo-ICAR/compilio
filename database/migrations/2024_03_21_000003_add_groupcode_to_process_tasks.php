<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            $table->string('groupcode')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            $table->dropColumn('groupcode');
        });
    }
};
