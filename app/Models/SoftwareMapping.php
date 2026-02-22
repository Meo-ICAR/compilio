<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareMapping extends Model
{
    //
    public function softwareApplication()
    {
        return $this->belongsTo(SoftwareApplication::class);
    }
}
