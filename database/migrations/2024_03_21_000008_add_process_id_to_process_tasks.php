<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('process_id')->nullable()->after('taskable_id');
            
            // Add foreign key constraint
            $table->foreign('process_id')
                  ->references('id')
                  ->on('processes')
                  ->onDelete('cascade');
            
            // Add index for performance
            $table->index('process_id');
        });
    }

    public function down(): void
    {
        Schema::table('process_tasks', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
            $table->dropIndex(['process_id']);
            $table->dropColumn('process_id');
        });
    }
};
