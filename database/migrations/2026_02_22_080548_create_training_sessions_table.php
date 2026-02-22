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
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->comment('Sessioni reali di formazione erogate o pianificate dalle agenzie.');
            $table->increments('id')->comment('ID univoco sessione');
            $table->char('company_id', 36)->index('company_id')->comment('Tenant che organizza o acquista la formazione');
            $table->unsignedInteger('training_template_id')->index('training_template_id')->comment('Riferimento al template del corso');
            $table->string('name')->comment('Nome specifico (es. Sessione Autunnale OAM Roma)');
            $table->decimal('total_hours', 5)->comment('Numero ore effettive erogate in questa sessione');
            $table->string('trainer_name')->nullable()->comment('Nome del docente o ente formatore');
            $table->date('start_date')->comment('Data inizio corso');
            $table->date('end_date')->comment('Data fine corso');
            $table->enum('location', ['ONLINE', 'PRESENZA', 'IBRIDO'])->nullable()->default('ONLINE');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
