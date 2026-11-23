<?php

namespace App\Services\Theme;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ThemeManager
{
    protected string $activeTheme;
    protected string $themesPath;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
        $this->activeTheme = SiteSetting::get('active_theme', config('aether.theme', 'aether-dark'));
    }

    public function getActiveTheme(): string
    {
        return $this->activeTheme;
    }

    public function setActiveTheme(string $theme): bool
    {
        if (!$this->themeExists($theme)) {
            return false;
        }

        SiteSetting::set('active_theme', $theme);
        $this->activeTheme = $theme;

        return true;
    }

    public function themeExists(string $theme): bool
    {
        return $theme === 'aether-dark' || File::isDirectory($this->themesPath . DIRECTORY_SEPARATOR . $theme);
    }

    public function getInstalledThemes(): array
    {
        $themes = [
            'aether-dark' => [
                'name' => 'Aether Dark',
                'description' => 'Official sleek MMORPG dark navy theme with blue/cyan ethereal glow.',
                'version' => '1.0.0',
                'author' => 'AetherCP Team',
                'is_active' => ($this->activeTheme === 'aether-dark'),
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
                        'is_active' => ($this->activeTheme === $slug),
                        'is_core' => false,
                    ];
                }
            }
        }

        return $themes;
    }
}

