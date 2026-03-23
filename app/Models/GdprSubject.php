<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GdprSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subjectable_id',
        'subjectable_type',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function subjectable(): MorphTo
    {
        return $this->morphTo();
    }
}
