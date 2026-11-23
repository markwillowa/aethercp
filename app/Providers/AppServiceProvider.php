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
        // Safe fallback runtime encryption key if APP_KEY is empty (enables web installer on fresh shared hosting)
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:c3RhdGljLWZhbGxiYWNrLWtleS1mb3ItaW5zdGFsbGF0aW9uLTEyMzQ1Nzg=']);
        }

        $themeManager->boot();
    }
}
