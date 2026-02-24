<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class CompanyWebsite extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'name',
        'domain',
        'type',
        'principal_id',
        'is_active',
        'url_privacy',
        'url_cookies',
        'is_footercompilant',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_footercompilant' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }
}
