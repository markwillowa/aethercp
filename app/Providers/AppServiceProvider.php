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
        // When not installed, force session and cache to file so unmigrated database doesn't crash on sessions table
        if (!file_exists(storage_path('installed'))) {
            config([
                'session.driver' => 'file',
                'cache.default' => 'file',
            ]);
        }

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

        // Dynamically synchronize runtime configuration from database site_settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
                $dbSettings = \App\Models\SiteSetting::all()->pluck('value', 'key');
                if (!empty($dbSettings['server_name'])) {
                    config(['aether.server_name' => $dbSettings['server_name']]);
                }
                if (!empty($dbSettings['tagline'])) {
                    config(['aether.tagline' => $dbSettings['tagline']]);
                }
                if (!empty($dbSettings['base_rate'])) {
                    config(['aether.rates.base' => $dbSettings['base_rate']]);
                }
                if (!empty($dbSettings['job_rate'])) {
                    config(['aether.rates.job' => $dbSettings['job_rate']]);
                }
                if (!empty($dbSettings['drop_rate'])) {
                    config(['aether.rates.drop' => $dbSettings['drop_rate']]);
                }
                if (!empty($dbSettings['max_base_level'])) {
                    config(['aether.rates.max_base_level' => $dbSettings['max_base_level']]);
                }
                if (!empty($dbSettings['max_job_level'])) {
                    config(['aether.rates.max_job_level' => $dbSettings['max_job_level']]);
                }
                if (isset($dbSettings['discord_url'])) {
                    config(['aether.community.discord' => $dbSettings['discord_url']]);
                }
                if (isset($dbSettings['facebook_url'])) {
                    config(['aether.community.facebook' => $dbSettings['facebook_url']]);
                }
                if (isset($dbSettings['youtube_url'])) {
                    config(['aether.community.youtube' => $dbSettings['youtube_url']]);
                }
                if (isset($dbSettings['twitter_url'])) {
                    config(['aether.community.twitter' => $dbSettings['twitter_url']]);
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback during migration or installation
        }

        $themeManager->boot();
    }
}
