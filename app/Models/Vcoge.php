<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Vcoge extends Model
{
    protected $table = 'vcoge';

    protected $fillable = [
        'company_id',
        'mese',
        'entrata',
        'uscita',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
