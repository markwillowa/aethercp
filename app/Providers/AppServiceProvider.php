<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Theme\ThemeManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, function () {
            return new ThemeManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ThemeManager $themeManager): void
    {
        $themeManager->boot();
    }
}
