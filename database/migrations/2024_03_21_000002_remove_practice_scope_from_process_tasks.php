<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            // Make practice_scope_id nullable first
            $table->unsignedBigInteger('practice_scope_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            // Make practice_scope_id not nullable again
            $table->unsignedBigInteger('practice_scope_id')->nullable(false)->change();
        });
    }
};
