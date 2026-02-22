<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApiConfiguration extends Model
{
    use BelongsToCompany;

    protected $casts = [
        'api_key' => 'encrypted',
        'api_secret' => 'encrypted',
    ];

    //
}
