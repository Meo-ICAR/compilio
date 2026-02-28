<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'type',
        'description',
        'principal_id',
        'is_practice',
        'is_audit',
    ];

    protected $casts = [
        'type' => 'string',
        'is_practice' => 'boolean',
        'is_audit' => 'boolean',
    ];

    public function target()
    {
        // Questa checklist a chi appartiene? (Agente, Pratica, ecc.)
        return $this->morphTo();
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function items(): HasMany
    {
        return $this->checklistItems();
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
    }
}
