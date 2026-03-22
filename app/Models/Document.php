<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use BelongsToCompany, HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'company_id',
        'documentable_id',
        'documentable_type',
        'document_type_id',
        'name',
        'annotation',
        'description',
        'status',
        'url_document',
        'rejection_note',
        'verified_at',
        'verified_by',
        'uploaded_by',
        'is_template',
        'expires_at',
        'emitted_at',
        'docnumber',
        'emitted_by',
        'is_signed',
        'user_id',
        'abstract',
        'ai_abstract',
        'ai_confidence_score',
        'extracted_text',
        'metadata',
        'sharepoint_id',
        'file_hash',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'emitted_at' => 'date',
        'verified_at' => 'datetime',
        'is_signed' => 'boolean',
        'is_template' => 'boolean',
        'ai_confidence_score' => 'integer',
        'metadata' => 'array',
    ];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function documentStatus(): BelongsTo
    {
        return $this->belongsTo(DocumentStatus::class, 'status');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Ottieni il modello a cui il documento appartiene (Client, Project, ecc.)
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Controlla se il documento è stato verificato
     */
    public function isVerified(): bool
    {
        return $this->document_status_id !== null;
    }

    /**
     * Controlla se il documento è valido
     */
    public function isValid(): bool
    {
        return $this->documentStatus?->is_ok ?? false;
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('documents')
            ->useDisk('public')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    /**
     * Get document URL from media or url_document field
     */
    public function getUrlDocumentAttribute(): ?string
    {
        // Try to get URL from media first
        if ($this->hasMedia('documents')) {
            return $this->getFirstMediaUrl('documents');
        }

        // Fallback to url_document field
        return $this->attributes['url_document'] ?? null;
    }

    /**
     * Get document URL for viewing
     */
    public function getViewUrl(): ?string
    {
        return $this->url_document;
    }

    /**
     * Controlla se il documento è respinto
     */
    public function isRejected(): bool
    {
        return $this->documentStatus?->is_rejected ?? false;
    }
}
