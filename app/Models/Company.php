<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Company extends Model implements HasCurrentTenantLabel
{
    use HasUuids;

    protected $guarded = [];

    public function getCurrentTenantLabel(): string
    {
        return 'Company';
    }

    public function branches()
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function websites()
    {
        return $this->hasMany(CompanyWebsite::class);
    }
}
