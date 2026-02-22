<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Principal extends Model
{
    use BelongsToCompany;

    //

    public function contacts() { return $this->hasMany(PrincipalContact::class); }

    public function mandates() { return $this->hasMany(PrincipalMandate::class); }

    public function principalScopes() { return $this->hasMany(PrincipalScope::class); }
}
