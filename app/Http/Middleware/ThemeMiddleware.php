<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Theme\ThemeManager;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    public function __construct(
        protected ThemeManager $themeManager
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->themeManager->apply();

        return $next($request);
    }
}

