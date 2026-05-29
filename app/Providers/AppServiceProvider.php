<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\SiteSetting;
use App\Policies\MediaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Media::class, MediaPolicy::class);

        try {
            $site = SiteSetting::allCached();
            View::share('site', $site);
        } catch (\Throwable $e) {
            View::share('site', []);
        }
    }
}
