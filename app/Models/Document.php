<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use BelongsToCompany, HasUuids, InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'documentable_id',
        'documentable_type',
        'document_type_id',
        'name',
        'status',
        'is_template',
        'expires_at',
        'emitted_at',
        'docnumber',
        'emitted_by',
        'is_signed',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'emitted_at' => 'date',
        'is_signed' => 'boolean',
        'is_template' => 'boolean',
    ];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Ottieni il modello a cui il documento appartiene (Client, Project, ecc.)
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
