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
        Schema::create('gdpr_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['internal', 'processor', 'third_party']);  // Responsabile Esterno, Terzo, ecc.
            $table->string('country')->default('IT');
            $table->boolean('outside_eea')->default(false);  // Trasferimento Extra-UE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_recipients');
    }
};
