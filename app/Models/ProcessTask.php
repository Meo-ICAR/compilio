<?php

namespace App\Models;

use App\Models\ChecklistItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProcessTask extends Model
{
    //
    protected $fillable = [
        'taskable_id',
        'taskable_type',
        'process_id',
        'name',
        'groupcode',
        'code',
        'slug',
        'sort_order'
    ];

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public function businessFunctions(): BelongsToMany
    {
        return $this
            ->belongsToMany(BusinessFunction::class, 'raci_assignments')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function checklistItems(): HasMany
    {
        // Correliamo tramite il campo process_task_id della tabella checklist_items
        return $this->hasMany(ChecklistItem::class, 'process_task_id');
    }

    /**
     * Get checklist items also correlated by process_task_code (legacy support)
     */
    public function checklistItemsByCode(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'process_task_code', 'slug');
    }

    public function raciAssignments()
    {
        return $this->hasMany(RaciAssignment::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * Utility: Restituisce la matrice RACI strutturata per questo task
     */
    public function getRaciMatrix(): \Illuminate\Support\Collection
    {
        return $this->businessFunctions->map(function ($func) {
            return [
                'function_name' => $func->name,
                'function_code' => $func->code,
                'role' => $func->pivot->role,  // R, A, C o I
            ];
        });
    }
}
