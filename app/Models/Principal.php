<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    public function mandates()
    {
        return $this->hasMany(PrincipalMandate::class);
    }

    public function principalScopes()
    {
        return $this->hasMany(PrincipalScope::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(PrincipalEmployee::class);
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function principalContacts(): HasMany
    {
        return $this->hasMany(PrincipalContact::class);
    }
}
