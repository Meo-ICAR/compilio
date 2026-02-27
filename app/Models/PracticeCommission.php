<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PracticeCommission extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'practice_id',
        'agent_id',
        'principal_id',
        'proforma_id',
        'practice_commission_status_id',
        'amount',
        'percentage',
        'status',
        'notes',
        'name',
        'CRM_code',
        'tipo',
        'description',
        'invoice_number',
        'status_payment',
        'status_commission',
        'is_coordination',
        'is_storno',
        'is_enasarco',
        'is_client',
        'is_payment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'storno_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'inserted_at' => 'date',
        'status_at' => 'date',
        'storned_at' => 'date',
        'created_at' => 'datetime',
        'perfected_at' => 'date',
        'updated_at' => 'datetime',
        'cancellation_at' => 'date',
        'invoice_at' => 'date',
        'paided_at' => 'date',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function proforma()
    {
        return $this->belongsTo(Proforma::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function practiceCommissionStatus()
    {
        return $this->belongsTo(PracticeCommissionStatus::class);
    }

    public function isPerfected()
    {
        return $this->practiceCommissionStatus?->is_perfectioned ?? false;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
