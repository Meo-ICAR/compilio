<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeStatus extends Model
{
    protected $table = 'practice_statuses';

    protected $fillable = [
        'name',
        'color',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
