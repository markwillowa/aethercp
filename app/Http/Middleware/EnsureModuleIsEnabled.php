<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleIsEnabled
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {}

    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!$this->moduleManager->isEnabled($module)) {
            abort(404, "The requested module [{$module}] has been disabled by the server administrator.");
        }

        return $next($request);
    }
}
