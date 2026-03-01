<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RopaEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'function_id',
        'processing_activity',
        'data_subjects',
        'data_categories',
        'purpose',
        'legal_basis',
        'recipients',
        'non_eu_transfer',
        'retention_period',
        'security_measures',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // Relazioni
    public function function(): BelongsTo
    {
        return $this->belongsTo(BusinessFunction::class, 'function_id');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isExpired(): bool
    {
        return $this->end_at && $this->end_at->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->start_at && $this->start_at->isFuture();
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Disattivata';
        }

        if ($this->isExpired()) {
            return 'Scaduta';
        }

        if ($this->isScheduled()) {
            return 'Programmata';
        }

        return 'Attiva';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Attiva' => 'success',
            'Disattivata' => 'danger',
            'Scaduta' => 'warning',
            'Programmata' => 'info',
            default => 'gray',
        };
    }

    public function getLegalBasisLabelAttribute(): string
    {
        return match ($this->legal_basis) {
            'Consenso' => "Consenso dell'interessato",
            'Esecuzione di un contratto' => 'Esecuzione di un contratto',
            'Obbligo di legge' => 'Obbligo di legge',
            'Legittimo interesse' => 'Legittimo interesse',
            'Interesse vitale' => 'Interesse vitale',
            'Interesse pubblico' => 'Interesse pubblico',
            default => $this->legal_basis,
        };
    }

    public function hasNonEuTransfer(): bool
    {
        return $this->non_eu_transfer && $this->non_eu_transfer !== 'Nessuno';
    }

    // Scope per filtrare
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeExpired($query)
    {
        return $query
            ->whereNotNull('end_at')
            ->where('end_at', '<', now());
    }

    public function scopeScheduled($query)
    {
        return $query
            ->whereNotNull('start_at')
            ->where('start_at', '>', now());
    }

    public function scopeByFunction($query, int $functionId)
    {
        return $query->where('function_id', $functionId);
    }

    public function scopeByLegalBasis($query, string $legalBasis)
    {
        return $query->where('legal_basis', $legalBasis);
    }

    public function scopeWithNonEuTransfer($query)
    {
        return $query->where('non_eu_transfer', '!=', 'Nessuno');
    }
}
