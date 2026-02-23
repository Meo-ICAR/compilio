<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'principal_id',
        'agent_id',
        'name',
        'CRM_code',
        'principal_code',
        'amount',
        'net',
        'brokerage_fee',
        'practice_scope_id',
        'status',
        'perfected_at',
        'is_active',
    ];

    protected $appends = ['clients_names'];

    protected $casts = [
        'status' => \App\Enums\PracticeStatus::class,
        'perfected_at' => 'date',
        'brokerage_fee' => 'decimal:2',
    ];

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_practice')->withPivot(['role', 'name', 'notes'])->withTimestamps();
    }

    public function practiceCommissions()
    {
        return $this->hasMany(PracticeCommission::class);
    }

    public function principal()
    {
        return $this->belongsTo(Principal::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function practiceScope()
    {
        return $this->belongsTo(PracticeScope::class);
    }

    public function getClientsNamesAttribute()
    {
        $clients = \DB::table('clients')
            ->join('client_practice', 'clients.id', '=', 'client_practice.client_id')
            ->where('client_practice.practice_id', $this->id)
            ->where('clients.company_id', $this->company_id)
            ->pluck('clients.name');

        return $clients->join(', ');
    }
}
