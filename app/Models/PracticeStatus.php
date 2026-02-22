<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeStatus extends Model
{
    protected $table = 'practice_status_lookup';

    protected $fillable = [
        'name',
        'color',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
