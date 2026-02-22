<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Employee extends Model
{
    use BelongsToCompany;

    //
}
