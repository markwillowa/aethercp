<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = file_exists(storage_path('installed'));

        // Allow static assets, build files, theme assets, and up checks without gating
        if ($request->is('themes/*/assets/*', 'build/*', 'up')) {
            return $next($request);
        }

        // If not installed, redirect all non-installer requests to the installer wizard
        if (!$isInstalled) {
            if (!$request->is('install*')) {
                return redirect()->route('install.index');
            }
            return $next($request);
        }

        // If already installed, block access to the installer wizard
        if ($request->is('install*')) {
            return redirect()->route('home')->with('error', 'AetherCP is already installed. To re-run setup, remove the storage/installed file.');
        }

        return $next($request);
    }
}
