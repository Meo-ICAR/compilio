<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeScope extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];
}
