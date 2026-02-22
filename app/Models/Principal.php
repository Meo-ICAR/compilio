<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Principal extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'abi',
        'mandate_number',
        'start_date',
        'type',
        'status',
    ];

    public function contacts()
    {
        return $this->hasMany(PrincipalContact::class);
    }

    public function mandates()
    {
        return $this->hasMany(PrincipalMandate::class);
    }

    public function principalScopes()
    {
        return $this->hasMany(PrincipalScope::class);
    }
}
