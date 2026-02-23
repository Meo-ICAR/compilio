<?php

namespace App\Providers;

use App\Models\ActivityLog;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ActivityLog::creating(function (ActivityLog $activityLog) {
            if (Filament::getTenant()) {
                $activityLog->company_id = Filament::getTenant()->id;
            }
        });
    }
}
