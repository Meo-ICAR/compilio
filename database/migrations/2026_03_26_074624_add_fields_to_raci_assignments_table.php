<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raci_assignments', function (Blueprint $table) {
            $table->date('start_at')->nullable()->comment('Ruolo ricoperto nel periodo');
            $table->date('end_at')->nullable()->comment('Ruolo ricoperto nel periodo');
            $table->string('substitution_reason')->nullable()->comment('Causale sostituzione ruolo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raci_assignments', function (Blueprint $table) {
            $table->dropColumn(['start_at', 'end_at', 'substitution_reason']);
        });
    }
};
