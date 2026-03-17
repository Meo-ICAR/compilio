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
            $table->unsignedInteger('checklist_type_id')->nullable();
            $table->string('richiedente')->nullable();
            $table->string('protocollo')->nullable()->after('richiedente');
            $table->integer('duration')->nullable()->after('protocollo');
            $table->foreignId('user_id')->nullable()->after('duration');
            $table->timestamp('received_at')->nullable()->after('user_id');
            $table->timestamp('sended_at')->nullable()->after('received_at');
            $table->text('annotation')->nullable()->after('sended_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklists', function (Blueprint $table) {
            $table->dropForeign(['checklist_type_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'richiedente',
                'protocollo',
                'duration',
                'user_id',
                'received_at',
                'sended_at',
                'annotation'
            ]);
        });
    }
};
