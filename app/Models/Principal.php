<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'abi',
        'mandate_number',
        'start_date',
        'type',
        'principal_type',
        'status',
        'is_dummy',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_dummy' => 'boolean',
        'principal_type' => 'string',
    ];

    public function checklists()
    {
        // Un agente ha molte checklist (le sue copie assegnate)
        return $this->morphMany(Checklist::class, 'target');
    }

    public function mandates()
    {
        return $this->hasMany(PrincipalMandate::class);
    }

    public function principalScopes()
    {
        return $this->hasMany(PrincipalScope::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(PrincipalEmployee::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function principalContacts(): HasMany
    {
        return $this->hasMany(PrincipalContact::class);
    }

    // Helper methods per principal_type
    public function getPrincipalTypeLabelAttribute(): string
    {
        return match ($this->principal_type) {
            'no' => 'Non Specificato',
            'banca' => 'Banca',
            'assicurazione' => 'Compagnia Assicurativa',
            'agente' => 'Agente',
            'agente_captive' => 'Agente Captive',
            default => $this->principal_type,
        };
    }

    public function isBank(): bool
    {
        return $this->principal_type === 'banca';
    }

    public function isInsurance(): bool
    {
        return $this->principal_type === 'assicurazione';
    }

    public function isAgent(): bool
    {
        return $this->principal_type === 'agente';
    }

    public function isCaptiveAgent(): bool
    {
        return $this->principal_type === 'agente_captive';
    }

    public function isFinancialInstitution(): bool
    {
        return in_array($this->principal_type, ['banca', 'assicurazione']);
    }

    public function isAgentType(): bool
    {
        return in_array($this->principal_type, ['agente', 'agente_captive']);
    }

    // Scope per filtrare per tipologia
    public function scopeByPrincipalType($query, string $type)
    {
        return $query->where('principal_type', $type);
    }

    public function scopeBanks($query)
    {
        return $query->where('principal_type', 'banca');
    }

    public function scopeInsurances($query)
    {
        return $query->where('principal_type', 'assicurazione');
    }

    public function scopeAgents($query)
    {
        return $query->whereIn('principal_type', ['agente', 'agente_captive']);
    }
}
