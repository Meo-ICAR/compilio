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
        Schema::create('gdpr_data_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // es. Dati Identificativi, Dati Sanitari, Log IP
            $table->boolean('is_special_category')->default(false);  // Ex Art. 9 (sensibili)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdpr_data_categories');
    }
};
