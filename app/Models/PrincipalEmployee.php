<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class PrincipalEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'principal_id',
        'company_id',
        'personable_type',
        'personable_id',
        'usercode',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'num_iscr_intermediario',
        'num_iscr_collaboratori_ii_liv',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function personable(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper methods for backward compatibility
    public function employee()
    {
        return $this->personable_type === Employee::class ? $this->personable() : null;
    }

    public function agent()
    {
        return $this->personable_type === Agent::class ? $this->personable() : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            });
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query
            ->where('personable_type', Employee::class)
            ->where('personable_id', $employeeId);
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query
            ->where('personable_type', Agent::class)
            ->where('personable_id', $agentId);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->is_active &&
            $this->start_date <= $today &&
            ($this->end_date === null || $this->end_date >= $today);
    }

    public function getPersonTypeAttribute(): string
    {
        return match ($this->personable_type) {
            Employee::class => 'employee',
            Agent::class => 'agent',
            default => 'unknown'
        };
    }
}
