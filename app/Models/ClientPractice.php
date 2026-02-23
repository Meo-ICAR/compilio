<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPractice extends Model
{
    protected $table = 'client_practice';

    protected $fillable = [
        'practice_id',
        'client_id',
        'role',
        'name',
        'notes',
        'purpose_of_relationship',
        'funds_origin',
        'oam_delivered',
        'role_risk_level',
        'company_id',
    ];

    protected $casts = [
        'oam_delivered' => 'boolean',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
