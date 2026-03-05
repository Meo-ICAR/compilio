<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuiIntermediaris extends Model
{
    use HasFactory;

    protected $table = 'rui_intermediaris';

    protected $fillable = [
        'oss',
        'matricola',
        'codice_compagnia',
        'ragione_sociale',
    ];
}
