<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name', 'code', 'is_person', 'is_signed', 'is_stored', 'is_practice', 'duration', 'emitted_by', 'is_sensible'];

    protected $casts = [
        'is_person' => 'boolean',
        'is_signed' => 'boolean',
        'is_stored' => 'boolean',
        'is_practice' => 'boolean',
        'is_sensible' => 'boolean',
    ];

    public function scopes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DocumentScope::class, 'document_type_scope');
    }

    /**
     * Scope per documenti relativi alla pratica
     */
    public function scopePracticeRelated($query)
    {
        return $query->where('is_practice', true);
    }

    /**
     * Scope per documenti non relativi alla pratica
     */
    public function scopeGeneral($query)
    {
        return $query->where('is_practice', false);
    }

    /**
     * Controlla se il tipo documento è relativo alla pratica
     */
    public function isPracticeRelated(): bool
    {
        return $this->is_practice ?? false;
    }

    /**
     * Controlla se il tipo documento è generale
     */
    public function isGeneral(): bool
    {
        return !$this->isPracticeRelated();
    }
}
