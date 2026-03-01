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
        'document_type_id',
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

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    // Helper methods per document_type
    public function hasDocumentType(): bool
    {
        return !is_null($this->document_type_id);
    }

    public function getDocumentTypeNameAttribute(): string
    {
        return $this->documentType ? $this->documentType->name : 'Non Specificato';
    }

    public function getDocumentTypeCodeAttribute(): string
    {
        return $this->documentType ? $this->documentType->code : '';
    }

    // Scope per filtrare per document_type
    public function scopeByDocumentType($query, $documentTypeId)
    {
        return $query->where('document_type_id', $documentTypeId);
    }

    public function scopeWithDocumentType($query)
    {
        return $query->whereNotNull('document_type_id');
    }

    public function scopeWithoutDocumentType($query)
    {
        return $query->whereNull('document_type_id');
    }

    public function scopeForMonitoredDocuments($query)
    {
        return $query->whereHas('documentType', function ($q) {
            $q->where('is_monitored', true);
        });
    }
}
