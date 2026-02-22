<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class PracticeCommission extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'practice_id',
        'amount',
        'percentage',
        'status',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
