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
        Schema::table('checklists', function (Blueprint $table) {
            $table->unsignedBigInteger('checklist_padre_id')->nullable()->comment('ID checklist padre per relazione gerarchica');
            $table->foreign('checklist_padre_id')->references('id')->on('checklists')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropForeign(['checklist_padre_id']);
            $table->dropColumn('checklist_padre_id');
        });
    }
};
