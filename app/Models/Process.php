<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Process extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'groupcode',
        'periodicity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the process tasks for this process.
     */
    public function processTasks(): HasMany
    {
        return $this->hasMany(ProcessTask::class, 'taskable_id')
            ->where('taskable_type', self::class);
    }

    /**
     * Get all tasks that can be morphed to this process.
     */
    public function tasks(): MorphMany
    {
        return $this->morphMany(ProcessTask::class, 'taskable');
    }

    /**
     * Get the human readable periodicity label.
     */
    public function getPeriodicityLabelAttribute(): string
    {
        return match ($this->periodicity) {
            'once' => 'Una Tantum',
            'monthly' => 'Mensile',
            'quarterly' => 'Trimestrale',
            'semiannual' => 'Semestrale',
            'annual' => 'Annuale',
            default => ucfirst($this->periodicity),
        };
    }

    /**
     * Scope to get only active processes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get processes by periodicity.
     */
    public function scopeByPeriodicity($query, string $periodicity)
    {
        return $query->where('periodicity', $periodicity);
    }

    /**
     * Get processes that need to be executed based on their periodicity.
     */
    public function scopeNeedingExecution($query)
    {
        return $query->active()->where(function ($q) {
            $q->where('periodicity', 'once')
              ->orWhere('periodicity', 'monthly')
              ->orWhere('periodicity', 'quarterly')
              ->orWhere('periodicity', 'semiannual')
              ->orWhere('periodicity', 'annual');
        });
    }
}
