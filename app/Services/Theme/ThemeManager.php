<?php

namespace App\Services\Theme;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    protected string $activeTheme = 'aether-dark';
    protected string $themesPath;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
        $this->activeTheme = $this->getActiveTheme();
    }

    public function boot(): void
    {
        $this->apply();
    }

    public function apply(): void
    {
        $activeTheme = $this->getActiveTheme();

        if (app()->bound('view')) {
            $finder = app('view')->getFinder();
            $defaultPaths = [resource_path('views')];
            $themesRoot = base_path('themes');
            $customViewsPath = $themesRoot . DIRECTORY_SEPARATOR . $activeTheme . DIRECTORY_SEPARATOR . 'views';

            if ($activeTheme !== 'aether-dark' && is_dir($customViewsPath)) {
                $finder->setPaths([$customViewsPath, ...$defaultPaths]);
            } else {
                $finder->setPaths($defaultPaths);
            }
            $finder->flush();
        }

        View::share('currentTheme', $this->getActiveThemeData());
    }

    public function getActiveTheme(): string
    {
        try {
            return SiteSetting::get('active_theme', 'aether-dark') ?: 'aether-dark';
        } catch (\Throwable $e) {
            return 'aether-dark';
        }
    }

    public function getActiveThemeData(): array
    {
        $activeTheme = $this->getActiveTheme();

        if ($activeTheme === 'aether-dark') {
            return [
                'name' => 'Aether Dark',
                'slug' => 'aether-dark',
                'description' => 'Official sleek MMORPG dark navy theme with blue/cyan ethereal glow.',
                'version' => '1.0.0',
                'author' => 'AetherCP Team',
                'is_core' => true,
                'has_css' => false,
                'colors' => [
                    'primary' => '#06b6d4',
                    'accent' => '#3b82f6',
                    'background' => '#070a13',
                    'surface' => '#0f172a',
                ],
            ];
        }

        $manifestFile = $this->themesPath . DIRECTORY_SEPARATOR . $activeTheme . DIRECTORY_SEPARATOR . 'theme.json';
        if (File::exists($manifestFile)) {
            $data = json_decode(File::get($manifestFile), true) ?: [];
            $cssFile = $this->themesPath . DIRECTORY_SEPARATOR . $activeTheme . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'theme.css';
            $data['has_css'] = File::exists($cssFile);
            $data['is_core'] = false;
            $data['slug'] = $activeTheme;
            return $data;
        }

        return [
            'name' => ucfirst($activeTheme),
            'slug' => $activeTheme,
            'is_core' => false,
            'has_css' => false,
        ];
    }

    public function setActiveTheme(string $theme): bool
    {
        if (!$this->themeExists($theme)) {
            return false;
        }

        SiteSetting::set('active_theme', $theme);
        $this->activeTheme = $theme;

        // Apply immediately
        $this->apply();

        // Clear compiled blade views so the new theme takes effect immediately
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        return true;
    }

    public function themeExists(string $theme): bool
    {
        return $theme === 'aether-dark' || File::isDirectory($this->themesPath . DIRECTORY_SEPARATOR . $theme);
    }

    public function getInstalledThemes(): array
    {
        $activeTheme = $this->getActiveTheme();

        $themes = [
            'aether-dark' => [
                'name' => 'Aether Dark',
                'description' => 'Official sleek MMORPG dark navy theme with blue/cyan ethereal glow.',
                'version' => '1.0.0',
                'author' => 'AetherCP Team',
                'is_active' => ($activeTheme === 'aether-dark'),
                'is_core' => true,
            ],
        ];

        if (File::isDirectory($this->themesPath)) {
            $dirs = File::directories($this->themesPath);
            foreach ($dirs as $dir) {
                $slug = basename($dir);
                $manifestFile = $dir . DIRECTORY_SEPARATOR . 'theme.json';

                if (File::exists($manifestFile)) {
                    $manifest = json_decode(File::get($manifestFile), true) ?: [];
                    $themes[$slug] = [
                        'name' => $manifest['name'] ?? ucfirst($slug),
                        'description' => $manifest['description'] ?? 'Custom Community Theme',
                        'version' => $manifest['version'] ?? '1.0.0',
                        'author' => $manifest['author'] ?? 'Third Party',
                        'is_active' => ($activeTheme === $slug),
                        'is_core' => false,
                        'is_disabled' => false,
                    ];
                }
            }
        }

        return $themes;
    }
}

