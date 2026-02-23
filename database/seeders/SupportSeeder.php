<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\AuditItem;
use App\Models\Company;
use App\Models\Practice;
use App\Models\Proforma;
use App\Models\ProformaStatus;
use App\Models\TrainingSession;
use App\Models\TrainingTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $agentUser = User::where('email', 'agent@agency.com')->first();
        $practice = Practice::first();
        $admin = User::first();

        if (!$company || !$agentUser || !$practice || !$admin) {
            return;
        }

        // Proformas
        $status = ProformaStatus::first();
        $proforma = Proforma::firstOrCreate(
            ['company_id' => $company->id, 'agent_id' => $agentUser->id, 'month' => date('n'), 'year' => date('Y')],
            [
                'name' => 'Proforma ' . date('n/Y') . ' - Agente',
                'total_commissions' => 1250.5,
                'status' => $status ? $status->name : 'INSERITO',
            ]
        );

        // Practice Commission
        \App\Models\PracticeCommission::firstOrCreate(
            ['practice_id' => $practice->id, 'company_id' => $company->id],
            [
                'proforma_id' => $proforma->id,
                'agent_id' => $agentUser->id,
                'amount' => 1250.5,
                'description' => 'Provvigione Mese Corrente'
            ]
        );

        // Audits
        $audit = Audit::firstOrCreate(
            ['title' => 'Audit Annuale Trasparenza 2025', 'company_id' => $company->id],
            [
                'requester_type' => 'OAM',
                'start_date' => '2025-01-01',
                'status' => 'COMPLETATO',
                'overall_score' => 'Conforme'
            ]
        );

        AuditItem::firstOrCreate(
            ['audit_id' => $audit->id, 'auditable_type' => get_class($practice), 'auditable_id' => $practice->id],
            [
                'name' => 'Verifica Privacy Cliente',
                'result' => 'OK',
            ]
        );

        // Training
        $template = TrainingTemplate::firstOrCreate(
            ['name' => 'Aggiornamento OAM Base'],
            [
                'category' => 'OAM',
                'base_hours' => 30.0,
                'is_mandatory' => 1
            ]
        );

        $session = TrainingSession::firstOrCreate(
            ['training_template_id' => $template->id, 'company_id' => $company->id],
            [
                'name' => 'Sessione Autunnale OAM',
                'total_hours' => 30.0,
                'start_date' => '2024-09-01',
                'end_date' => '2024-09-30'
            ]
        );

        \App\Models\TrainingRecord::firstOrCreate(
            ['training_session_id' => $session->id, 'trainable_type' => 'App\Models\Agent', 'trainable_id' => 1],
            [
                'status' => 'COMPLETATO',
                'hours_attended' => 30.0,
                'score' => 'Idoneo',
                'completion_date' => '2024-10-01'
            ]
        );

        // API Configuration
        $software = \App\Models\SoftwareApplication::first();
        if ($software) {
            \App\Models\ApiConfiguration::firstOrCreate(
                ['software_application_id' => $software->id, 'company_id' => $company->id],
                [
                    'name' => 'Collega CRM Master',
                    'auth_type' => 'API_KEY',
                    'api_key' => 'scj4x8c39nxk21',
                    'api_secret' => 'super_secret',
                    'is_active' => 1
                ]
            );
        }
    }
}
