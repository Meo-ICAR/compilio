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
        Schema::table('audits', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('auditable_type')->nullable()->after('company_id')->comment('Classe del Modello collegato (es. App\Models\Company, App\Models\Agent, etc.)');
            $table->string('auditable_id', 36)->nullable()->after('auditable_type')->comment('ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)');

            // Add indexes for polymorphic columns
            $table->index(['auditable_type', 'auditable_id'], 'auditable_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex('auditable_index');
            $table->dropColumn('auditable_type');
            $table->dropColumn('auditable_id');
        });
    }
};
