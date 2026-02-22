<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Company extends Model implements HasCurrentTenantLabel
{
    use HasUuids;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'vat_number',
        'vat_name',
        'oam',
        'oam_at',
        'oam_name',
        'company_type_id',
    ];

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

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class);
    }

    public function softwareApplications()
    {
        return $this->belongsToMany(SoftwareApplication::class)
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }
}
