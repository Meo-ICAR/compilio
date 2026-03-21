<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->string('process_task_groupcode')->nullable()->after('business_function_id');
            
            // Add index for performance
            $table->index('process_task_groupcode');
        });
    }

    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropIndex(['process_task_groupcode']);
            $table->dropColumn('process_task_groupcode');
        });
    }
};
