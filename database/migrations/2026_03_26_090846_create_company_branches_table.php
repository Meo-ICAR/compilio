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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36)->nullable()->comment('ID azienda');
            $table->string('name', 255)->nullable()->comment('Nome della sede (es. Sede Centrale, Filiale Milano Nord)');
            $table->boolean('is_main_office')->default(false)->comment('Indica se questa è la sede legale/principale dell\'agenzia');
            $table->string('manager_first_name', 100)->nullable()->comment('Nome del referente/responsabile della sede');
            $table->string('manager_last_name', 100)->nullable()->comment('Cognome del referente/responsabile della sede');
            $table->string('manager_tax_code', 16)->nullable()->comment('Codice Fiscale del referente della sede');
            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'is_main_office'], 'idx_company_main');

            // Foreign key
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // Insert default data
        \DB::table('company_branches')->insert([
            'company_id' => 'd904fae6-702d-4965-95e5-667e066e46a8',
            'name' => 'Sede Legale Roma',
            'is_main_office' => true,
            'manager_first_name' => 'sergio',
            'manager_last_name' => 'Bracale',
            'manager_tax_code' => null,
            'created_at' => '2026-03-18 10:18:56',
            'updated_at' => '2026-03-18 10:18:56',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
