<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CompanyWebsite extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'domain',
        'type',
        'principal_id',
        'is_active',
        'is_typical',
        'privacy_date',
        'transparency_date',
        'privacy_prior_date',
        'transparency_prior_date',
        'url_privacy',
        'url_cookies',
        'is_footercompilant',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_typical' => 'boolean',
        'is_footercompilant' => 'boolean',
        'privacy_date' => 'date',
        'transparency_date' => 'date',
        'privacy_prior_date' => 'date',
        'transparency_prior_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    // Helper methods per privacy e trasparenza
    public function isPrivacyUpToDate(): bool
    {
        return $this->privacy_date && $this->privacy_date->greaterThan(now()->subYear());
    }

    public function isTransparencyUpToDate(): bool
    {
        return $this->transparency_date && $this->transparency_date->greaterThan(now()->subYear());
    }

    public function getPrivacyStatusAttribute(): string
    {
        if (!$this->privacy_date) {
            return 'Non Impostata';
        }
        if ($this->isPrivacyUpToDate()) {
            return 'Aggiornata';
        }
        return 'Da Aggiornare';
    }

    public function getTransparencyStatusAttribute(): string
    {
        if (!$this->transparency_date) {
            return 'Non Impostata';
        }
        if ($this->isTransparencyUpToDate()) {
            return 'Aggiornata';
        }
        return 'Da Aggiornare';
    }

    public function getPrivacyDaysSinceUpdateAttribute(): int
    {
        return $this->privacy_date ? $this->privacy_date->diffInDays(now()) : 0;
    }

    public function getTransparencyDaysSinceUpdateAttribute(): int
    {
        return $this->transparency_date ? $this->transparency_date->diffInDays(now()) : 0;
    }

    // Scope per filtrare
    public function scopeTypical($query)
    {
        return $query->where('is_typical', true);
    }

    public function scopeNotTypical($query)
    {
        return $query->where('is_typical', false);
    }

    public function scopePrivacyExpired($query)
    {
        return $query->where(function ($q) {
            $q
                ->whereNull('privacy_date')
                ->orWhere('privacy_date', '<', now()->subYear());
        });
    }

    public function scopeTransparencyExpired($query)
    {
        return $query->where(function ($q) {
            $q
                ->whereNull('transparency_date')
                ->orWhere('transparency_date', '<', now()->subYear());
        });
    }
}
