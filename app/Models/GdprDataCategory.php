<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GdprDataCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_special_category',
    ];

    protected $casts = [
        'is_special_category' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
