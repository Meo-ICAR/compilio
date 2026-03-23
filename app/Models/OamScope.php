<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OamScope extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'tipo_prodotto',
    ];

    protected $casts = [
        'tipo_prodotto' => 'array',
    ];

    public function oams()
    {
        return $this
            ->belongsToMany(Oam::class, 'oam_scope')
            ->withTimestamps();
    }

    public function practiceScopes()
    {
        return $this->hasMany(PracticeScope::class, 'oam_code', 'code');
    }
}
