<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareCategory extends Model
{
    //
    public function softwareApplications()
    {
        return $this->hasMany(SoftwareApplication::class, 'category_id');
    }
}
