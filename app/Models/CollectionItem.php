<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id',
        'name',
        'question',
        'answer',
        'description',
        'annotation',
        'is_required',
        'attach_model',
        'attach_model_id',
        'is_document_required',
        'repeatable_code',
        'item_code',
        'depends_on_code',
        'depends_on_value',
        'dependency_type',
        'url_step',
        'url_callback',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_document_required' => 'boolean',
        'attach_model' => 'string',
        'checklist_id' => 'integer',
        'dependency_type' => 'string',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}
