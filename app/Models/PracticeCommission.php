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
        'amount',
        'percentage',
        'status',
        'notes',
        'name',
        'CRM_code',
        'tipo',
        'description',
        'is_coordination',
        'invoice_number',
        'is_storno',
        'is_enasarco',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'storno_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
