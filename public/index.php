<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Handle clean subdirectory rewriting (e.g. http://localhost/aether instead of /aether/public)
if (isset($_SERVER['SCRIPT_NAME']) && str_ends_with($_SERVER['SCRIPT_NAME'], '/public/index.php')) {
    if (!str_contains($_SERVER['REQUEST_URI'] ?? '', '/public')) {
        $_SERVER['SCRIPT_NAME'] = dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/index.php';
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
