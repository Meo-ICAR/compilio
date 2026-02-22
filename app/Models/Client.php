<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Client extends Model implements HasMedia
{
    use BelongsToCompany, InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'is_person',
        'name',
        'first_name',
        'tax_code',
        'email',
        'phone',
        'is_pep',
        'client_type_id',
        'is_sanctioned',
    ];

    public function addresses(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }
}
