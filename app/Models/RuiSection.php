<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiSection extends Model
{
    use HasFactory;

    protected $connection = 'unicodb';

    protected $fillable = [
        'sezione',
        'categoria',
        'descrizione',
    ];
}
