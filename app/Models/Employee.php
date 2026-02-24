<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'company_branch_id',
        'coordinated_by_id',
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
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function companyBranch(): BelongsTo
    {
        return $this->belongsTo(CompanyBranch::class);
    }

    public function coordinatedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'coordinated_by_id');
    }

    public function coordinatedEmployees()
    {
        return $this->hasMany(Employee::class, 'coordinated_by_id');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function scopeSameBranchCoordinators($query)
    {
        return $query
            ->where('company_branch_id', $this->company_branch_id)
            ->where('id', '!=', $this->id);
    }
}
