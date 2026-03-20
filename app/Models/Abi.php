<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abi extends Model
{
    protected $connection = 'unicodb';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];
}
