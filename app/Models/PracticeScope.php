<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeScope extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'oam_code',
        'is_oneclient'
    ];

    public function oamScope()
    {
        return $this->belongsTo(OamScope::class, 'oam_code');
    }

    public function oamName()
    {
        return $this->oamScope ? ($this->oamScope->code . ' ' . $this->oamScope->name) : null;
    }
}
