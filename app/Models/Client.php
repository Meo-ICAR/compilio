<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Client extends Model implements HasMedia
{
    use BelongsToCompany, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'company_id',
        'is_person',
        'name',
        'first_name',
        'tax_code',
        'email',
        'phone',
        'is_pep',
        'client_type_id',
        'is_sanctioned',
        'privacy_consent',
    ];

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('documents')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/jpg',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])
            ->useDisk('public');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function members()
    {
        return $this
            ->belongsToMany(Client::class, 'client_relations', 'company_id', 'client_id')
            ->withPivot('shares_percentage', 'is_titolare', 'client_type_id', 'data_inizio_ruolo', 'data_fine_ruolo')
            ->withTimestamps();
    }

    public function companyRelations()
    {
        return $this->hasMany(ClientRelation::class, 'company_id');
    }

    public function personRelations()
    {
        return $this->hasMany(ClientRelation::class, 'client_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
