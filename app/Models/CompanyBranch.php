<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'is_main_office',
        'manager_first_name',
        'manager_last_name',
        'manager_tax_code',
    ];

    protected $casts = [
        'is_main_office' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessors
    public function getManagerFullNameAttribute(): string
    {
        return trim(($this->manager_first_name ?? '') . ' ' . ($this->manager_last_name ?? ''));
    }

    public function getManagerDisplayNameAttribute(): string
    {
        return $this->manager_full_name ?: 'Nessun responsabile';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->is_main_office ? 'Sede Legale' : 'Filiale';
    }

    public function getAddressStringAttribute(): string
    {
        return $this->address ? $this->address->full_address : 'Nessun indirizzo';
    }

    // Scopes
    public function scopeMain($query)
    {
        return $query->where('is_main_office', true);
    }

    public function scopeSecondary($query)
    {
        return $query->where('is_main_office', false);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeWithManager($query)
    {
        return $query
            ->whereNotNull('manager_first_name')
            ->whereNotNull('manager_last_name');
    }

    // Business logic methods
    public function hasManager(): bool
    {
        return !empty($this->manager_first_name) && !empty($this->manager_last_name);
    }

    public function isMain(): bool
    {
        return $this->is_main_office;
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_main_office;
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    // Boot method per gestire il salvataggio automatico dell'indirizzo
    protected static function booted()
    {
        static::saved(function ($companyBranch) {
            // Se ci sono dati dell'indirizzo nel request, salvali
            if (request()->has('address')) {
                $addressData = request()->input('address');

                $address = $companyBranch->address ?? new Address();
                $address->addressable_type = CompanyBranch::class;
                $address->addressable_id = $companyBranch->id;
                $address->fill($addressData);
                $address->save();
            }
        });
    }
}
