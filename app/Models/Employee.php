<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'role',
        'department',
        'hire_date',
        'employment_type_id',
    ];

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class);
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function morphTrainingRecords(): MorphMany
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }
}
