<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Practice extends Model implements HasMedia
{
    use BelongsToCompany, InteractsWithMedia;

    protected $fillable = [
        'company_id',
        'principal_id',
        'agent_id',
        'name',
        'CRM_code',
        'principal_code',
        'amount',
        'net',
        'practice_scope_id',
        'status',
        'perfected_at',
        'is_active',
    ];

    protected $appends = ['clients_names'];

    protected $casts = [
        'status' => \App\Enums\PracticeStatus::class,
    ];

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Document::class);
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
