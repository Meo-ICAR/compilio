<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPractice extends Model
{
    protected $table = 'client_practice';

    protected $fillable = [
        'practice_id',
        'client_id',
        'role',
        'name',
        'notes',
        'company_id',
    ];
}
