<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'abi',
        'mandate_number',
        'start_date',
        'type',
        'status',
        'oam_number',
        'oam_at',
        'oam_name',
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function trainingRecords(): MorphMany
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }
}
