<?php

namespace App\Models;

use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;  // <--- Deve esserci
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasCurrentTenantLabel, HasMedia
{
    use HasUuids, InteractsWithMedia, HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'name',
        'vat_number',
        'vat_name',
        'oam',
        'oam_at',
        'oam_name',
        'company_type_id',
        'page_header',
        'page_footer',
    ];

    public function getCurrentTenantLabel(): string
    {
        return 'Company';
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('logo')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/svg+xml',
                'image/webp',
            ])
            ->useDisk('public')
            ->singleFile()
            ->registerMediaConversions(function ($media) {
                $this
                    ->addMediaConversion('thumb')
                    ->width(200)
                    ->height(200)
                    ->sharpen(10);
            });
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo') ?? asset('images/default-logo.png');
    }

    public function getLogoThumbUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('logo', 'thumb') ?? asset('images/default-logo.png');
    }

    public function branches()
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function websites()
    {
        return $this->hasMany(CompanyWebsite::class);
    }

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class);
    }

    public function softwareApplications()
    {
        return $this
            ->belongsToMany(SoftwareApplication::class)
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function trainingRecords(): MorphMany
    {
        return $this->morphMany(TrainingRecord::class, 'trainable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function companyClients(): HasMany
    {
        return $this->hasMany(CompanyClient::class);
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(CompanyWallet::class);
    }

    public function apiUsageLogs(): HasMany
    {
        return $this->hasMany(CompanyApiUsageLog::class);
    }
}
