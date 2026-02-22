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
        Schema::create('client_privacies', function (Blueprint $table) {
            $table->increments('id');
            $table->char('company_id', 36)->index()->comment('Tentant ID');
            $table->unsignedInteger('client_id')->index()->comment('Riferimento al cliente');
            $table->string('request_type')->comment('Accesso, Rettifica, Cancellazione, Portabilità');
            $table->string('status')->comment('Ricevuta, In lavorazione, Evasa');
            $table->timestamp('completed_at')->nullable()->comment('Data della risposta definitiva');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_privacies');
    }
};
