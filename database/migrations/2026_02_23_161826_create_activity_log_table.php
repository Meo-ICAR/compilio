<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index('log_name');

            $table->unsignedInteger('practice_id')->comment('Riferimento alla pratica')->nullable();

            $table->unsignedInteger('client_id')->comment('Riferimento al cliente tramite il mandato')->nullable();

            // Dati dell'evento intercettato
            $table->enum('tipo_evento', ['instaurazione_rapporto', 'esecuzione_operazione', 'chiusura_rapporto'])->nullable();
            $table->date('data_evento')->nullable();
            $table->decimal('importo_rilevato', 15, 2)->nullable();

            // Fotografia dei dati in formato JSON per averli pronti
            $table->json('payload_dati_cliente')->nullable();

            // Stato di consolidamento
            $table->enum('stato', ['da_consolidare', 'consolidato', 'scartato'])->default('da_consolidare');
            $table->text('note_operatore')->nullable();

            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
        });
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
