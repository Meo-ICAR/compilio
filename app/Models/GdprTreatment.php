<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class GdprTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'controller_id',
        'title',
        'purposes',
        'legal_basis',
        'retention_period',
        'retention_criteria',
        'security_measures',
        'has_dpia',
        'last_review_at',
    ];

    protected $casts = [
        'security_measures' => 'array',
        'has_dpia' => 'boolean',
        'last_review_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function controller(): BelongsTo
    {
        return $this->belongsTo(GdprController::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(GdprSubject::class, 'gdpr_treatment_subject');
    }

    public function dataCategories(): BelongsToMany
    {
        return $this->belongsToMany(GdprDataCategory::class, 'gdpr_treatment_data_category');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(GdprRecipient::class, 'gdpr_treatment_recipient');
    }
}
