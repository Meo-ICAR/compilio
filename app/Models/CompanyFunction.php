<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CompanyFunction extends Model
{
    use HasFactory;

    protected $table = 'company_functions';

    protected $fillable = [
        'company_id',
        'business_function_id',
        'employee_id',
        'client_id',
        'is_privacy',
        'is_outsourced',
        'report_frequency',
        'contract_expiry_date',
        'notes',
    ];

    protected $casts = [
        'is_privacy' => 'boolean',
        'is_outsourced' => 'boolean',
        'contract_expiry_date' => 'date',
    ];

    // Relazioni
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function businessFunction(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class, 'business_function_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Helper methods
    public function isOutsourced(): bool
    {
        return $this->is_outsourced;
    }

    public function isInternal(): bool
    {
        return !$this->is_outsourced;
    }

    public function isPrivacy(): bool
    {
        return $this->is_privacy;
    }

    public function hasEmployee(): bool
    {
        return !is_null($this->employee_id);
    }

    public function hasClient(): bool
    {
        return !is_null($this->client_id);
    }

    public function getContractStatusAttribute(): string
    {
        if (!$this->is_outsourced) {
            return 'Interno';
        }

        if (!$this->contract_expiry_date) {
            return 'Esternalizzato (senza scadenza)';
        }

        if ($this->contract_expiry_date->isPast()) {
            return 'Esternalizzato (scaduto)';
        }

        $daysUntilExpiry = $this->contract_expiry_date->diffInDays(now());
        if ($daysUntilExpiry <= 30) {
            return 'Esternalizzato (in scadenza)';
        }

        return 'Esternalizzato (attivo)';
    }

    public function getContractStatusColorAttribute(): string
    {
        return match ($this->contract_status) {
            'Interno' => 'success',
            'Esternalizzato (attivo)' => 'primary',
            'Esternalizzato (in scadenza)' => 'warning',
            'Esternalizzato (scaduto)' => 'danger',
            default => 'gray',
        };
    }

    public function isContractExpiring(): bool
    {
        return $this->is_outsourced &&
            $this->contract_expiry_date &&
            $this->contract_expiry_date->diffInDays(now()) <= 30;
    }

    public function isContractExpired(): bool
    {
        return $this->is_outsourced &&
            $this->contract_expiry_date &&
            $this->contract_expiry_date->isPast();
    }

    // Scope per filtrare
    public function scopeOutsourced($query)
    {
        return $query->where('is_outsourced', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_outsourced', false);
    }

    public function scopePrivacy($query)
    {
        return $query->where('is_privacy', true);
    }

    public function scopeNotPrivacy($query)
    {
        return $query->where('is_privacy', false);
    }

    public function scopeByCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByBusinessFunction($query, int $functionId)
    {
        return $query->where('business_function_id', $functionId);
    }

    public function scopeWithInternalEmployee($query)
    {
        return $query->whereNotNull('employee_id');
    }

    public function scopeWithExternalClient($query)
    {
        return $query->whereNotNull('client_id');
    }

    public function scopeContractExpiring($query, int $days = 30)
    {
        return $query
            ->where('is_outsourced', true)
            ->whereNotNull('contract_expiry_date')
            ->whereDate('contract_expiry_date', '<=', now()->addDays($days));
    }

    public function scopeContractExpired($query)
    {
        return $query
            ->where('is_outsourced', true)
            ->whereNotNull('contract_expiry_date')
            ->whereDate('contract_expiry_date', '<', now());
    }
}
