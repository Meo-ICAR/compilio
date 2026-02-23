<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name', 'is_person', 'is_signed', 'is_stored', 'duration', 'emitted_by', 'is_sensible'];

    public function scopes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DocumentScope::class, 'document_type_scope');
    }
}
