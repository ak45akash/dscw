<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::before(function ($user, $ability) {
            if ($user?->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        View::composer('components.layouts.admin', function ($view) {
            if (auth()->check()) {
                $view->with('adminNavigation', \App\Support\AdminNavigation::visibleForUser(auth()->user()));
            }
        });
    }
}
