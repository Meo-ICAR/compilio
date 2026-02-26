<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'coordinated_by_id',
        'coordinated_by_agent_id',
        'name',
        'abi',
        'mandate_number',
        'start_date',
        'type',
        'status',
        'oam_number',
        'oam_at',
        'oam_name',
        'vat_number',
        'vat_name',
        'is_active',
        'user_id',
        'description',
        'enasarco',
    ];

    protected $casts = [
        'oam_at' => 'date',
        'stipulated_at' => 'date',
        'dismissed_at' => 'date',
        'contribute' => 'decimal:2',
        'remburse' => 'decimal:2',
        'contributeFrom' => 'date',
        'contributeFrequency' => 'integer',
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function coordinatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'coordinated_by_id');
    }

    public function coordinatedByAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'coordinated_by_agent_id');
    }

    public function coordinatedAgents()
    {
        return $this->hasMany(Agent::class, 'coordinated_by_agent_id');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function trainingRecords(): MorphMany
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
