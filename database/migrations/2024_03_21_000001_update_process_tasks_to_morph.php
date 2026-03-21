<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            // Add morph columns
            $table->unsignedBigInteger('taskable_id')->nullable()->after('id');
            $table->string('taskable_type')->nullable()->after('taskable_id');
            
            // Add index for performance
            $table->index(['taskable_type', 'taskable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            // Drop morph columns
            $table->dropIndex(['taskable_type', 'taskable_id']);
            $table->dropColumn(['taskable_type', 'taskable_id']);
        });
    }
};
