<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('principal_employees', function (Blueprint $table) {
            // Add morphable columns for employee/agent if they don't exist
            $columns = DB::getSchemaBuilder()->getColumnListing('principal_employees');
            
            if (!in_array('personable_type', $columns)) {
                $table->string('personable_type')->nullable()->after('company_id');
            }
            
            if (!in_array('personable_id', $columns)) {
                $table->unsignedBigInteger('personable_id')->nullable()->after('personable_type');
            }
            
            // Add OAM-related columns if they don't exist
            if (!in_array('num_iscr_intermediario', $columns)) {
                $table->string('num_iscr_intermediario')->nullable()->after('personable_id');
            }
            
            if (!in_array('num_iscr_collaboratori_ii_liv', $columns)) {
                $table->string('num_iscr_collaboratori_ii_liv')->nullable()->after('num_iscr_intermediario');
            }
            
            // Add indexes if they don't exist
            $indexes = collect(DB::select("SHOW INDEX FROM principal_employees"))->pluck('Key_name')->unique();
            
            if (!$indexes->contains('principal_employees_personable_type_personable_id_index')) {
                $table->index(['personable_type', 'personable_id']);
            }
            
            if (!$indexes->contains('principal_employees_company_id_is_active_index')) {
                $table->index(['company_id', 'is_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('principal_employees', function (Blueprint $table) {
            $columns = DB::getSchemaBuilder()->getColumnListing('principal_employees');
            
            // Drop new columns if they exist
            if (in_array('personable_type', $columns)) {
                $table->dropColumn('personable_type');
            }
            
            if (in_array('personable_id', $columns)) {
                $table->dropColumn('personable_id');
            }
            
            if (in_array('num_iscr_intermediario', $columns)) {
                $table->dropColumn('num_iscr_intermediario');
            }
            
            if (in_array('num_iscr_collaboratori_ii_liv', $columns)) {
                $table->dropColumn('num_iscr_collaboratori_ii_liv');
            }
        });
    }
};
