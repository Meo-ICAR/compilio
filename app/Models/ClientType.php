<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_person',
        'is_company',
    ];
}
