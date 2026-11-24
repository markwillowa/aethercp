<?php

namespace App\Services\Module;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\File;

class ModuleManager
{
    protected string $modulesPath;

    public function __construct()
    {
        $this->modulesPath = base_path('modules');
    }

    public function getModules(): array
    {
        // 1. Core modules defined in configuration
        $configured = config('modules.modules', []);
        $modules = [];

        foreach ($configured as $key => $mod) {
            $enabledSetting = SiteSetting::get("module_enabled_{$key}", null);
            if ($key === 'database' && $enabledSetting === null) {
                $enabledSetting = SiteSetting::get("module_enabled_itemdb", null);
            }
            $isEnabled = $enabledSetting !== null ? (bool) $enabledSetting : (bool) ($mod['enabled'] ?? true);

            $modules[$key] = array_merge($mod, [
                'key' => $key,
                'is_core' => true,
                'enabled' => $isEnabled,
                'has_update' => false,
            ]);
        }

        // 2. Discover file-based custom extensions safely
        if (File::isDirectory($this->modulesPath)) {
            $dirs = File::directories($this->modulesPath);
            foreach ($dirs as $dir) {
                $key = basename($dir);
                $manifestPath = $dir . DIRECTORY_SEPARATOR . 'module.json';

                if (File::exists($manifestPath)) {
                    $manifest = json_decode(File::get($manifestPath), true) ?: [];
                    $enabledSetting = SiteSetting::get("module_enabled_{$key}", null);
                    $isEnabled = $enabledSetting !== null ? (bool) $enabledSetting : false;

                    $modules[$key] = [
                        'key' => $key,
                        'name' => $manifest['name'] ?? ucfirst($key),
                        'description' => $manifest['description'] ?? 'Extension Module',
                        'version' => $manifest['version'] ?? '1.0.0',
                        'author' => $manifest['author'] ?? 'Community Developer',
                        'is_core' => false,
                        'enabled' => $isEnabled,
                        'dependencies' => $manifest['dependencies'] ?? [],
                    ];
                }
            }
        }

        return $modules;
    }

    public function isEnabled(string $key): bool
    {
        $modules = $this->getModules();
        if ($key === 'itemdb' || $key === 'database') {
            if (isset($modules['database'])) {
                return (bool) $modules['database']['enabled'];
            }
            if (isset($modules['itemdb'])) {
                return (bool) $modules['itemdb']['enabled'];
            }
            return true;
        }

        return isset($modules[$key]) && (bool) $modules[$key]['enabled'];
    }

    public function toggle(string $key): bool
    {
        $modules = $this->getModules();
        if (!isset($modules[$key])) {
            if ($key === 'itemdb' && isset($modules['database'])) {
                $key = 'database';
            } elseif ($key === 'database' && isset($modules['itemdb'])) {
                $key = 'itemdb';
            } else {
                return false;
            }
        }

        // Prevent disabling protected core modules like account system
        if (!empty($modules[$key]['protected'])) {
            return false;
        }

        $currentState = (bool) $modules[$key]['enabled'];
        $newState = !$currentState;

        SiteSetting::set("module_enabled_{$key}", $newState ? '1' : '0');
        if ($key === 'database') {
            SiteSetting::set("module_enabled_itemdb", $newState ? '1' : '0');
        } elseif ($key === 'itemdb') {
            SiteSetting::set("module_enabled_database", $newState ? '1' : '0');
        }

        return $newState;
    }
}

