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
        Schema::table('employment_types', function (Blueprint $table) {
            $table->dropColumn('client_type_id');
        });

        Schema::table('employment_types', function (Blueprint $table) {
            $table->unsignedInteger('client_type_id')->nullable();
            $table->foreign('client_type_id')->references('id')->on('client_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employment_types', function (Blueprint $table) {
            $table->dropForeign('employment_types_client_type_id_foreign');
            $table->dropColumn('client_type_id');
        });
    }
};
