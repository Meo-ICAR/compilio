<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'requester_type',
        'principal_id',
        'agent_id',
        'regulatory_body_id',
        'client_id',
        'title',
        'emails',
        'reference_period',
        'start_date',
        'end_date',
        'status',
        'overall_score',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function regulatoryBody()
    {
        return $this->belongsTo(RegulatoryBody::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
