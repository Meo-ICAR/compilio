<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'is_company',
        'is_lead',
        'leadsource_id',
        'acquired_at',
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

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'leadsource_id');
    }

    public function leadSourceClients()
    {
        return $this->hasMany(Client::class, 'leadsource_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
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

        $this
            ->addMediaCollection('photos')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/webp',
                'image/svg+xml',
            ])
            ->useDisk('public')
            ->registerMediaConversions(function ($media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(150)
                    ->height(150)
                    ->sharpen(10)
                    ->crop('crop-center');

                $this
                    ->addMediaConversion('medium')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10);

                $this
                    ->addMediaConversion('large')
                    ->width(800)
                    ->height(600)
                    ->sharpen(10);
            });
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

    public function getPhotoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos') ?? asset('images/default-avatar.png');
    }

    public function getPhotoThumbUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos', 'thumb') ?? asset('images/default-avatar.png');
    }

    public function getPhotoMediumUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos', 'medium') ?? asset('images/default-avatar.png');
    }

    public function getPhotoLargeUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photos', 'large') ?? asset('images/default-avatar.png');
    }

    public function hasPhoto(): bool
    {
        return $this->hasMedia('photos');
    }
}
