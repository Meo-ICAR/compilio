<?php

namespace App\Traits;

use App\Models\Company;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (Filament::hasTenant()) {
                $builder->where('company_id', Filament::getTenant()->id);
            }
        });

        static::creating(function ($model) {
            if (Filament::hasTenant() && empty($model->company_id)) {
                $model->company_id = Filament::getTenant()->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
