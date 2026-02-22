<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareApplication extends Model
{
    //
    public function apiConfigurations()
    {
        return $this->hasMany(ApiConfiguration::class);
    }

    public function softwareMappings()
    {
        return $this->hasMany(SoftwareMapping::class);
    }

    public function softwareCategory()
    {
        return $this->belongsTo(SoftwareCategory::class, 'category_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class)
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }
}
