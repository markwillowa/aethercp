<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\RankingController;
use App\Http\Controllers\Public\ItemDbController;
use App\Http\Controllers\Public\DownloadController;
use App\Http\Controllers\Public\DonationController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// News & CMS
Route::middleware('module:news')->group(function () {
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
});

// Rankings
Route::middleware('module:rankings')->group(function () {
    Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');
});

// Guilds & Emblems
Route::middleware('module:guild')->group(function () {
    Route::get('/guilds', [\App\Http\Controllers\Public\GuildController::class, 'index'])->name('guilds.index');
    Route::get('/guilds/{id}', [\App\Http\Controllers\Public\GuildController::class, 'show'])->name('guilds.show')->where('id', '[0-9]+');
    Route::get('/guilds/{id}/emblem', [\App\Http\Controllers\Public\GuildController::class, 'emblem'])->name('guilds.emblem')->where('id', '[0-9]+');
});

// Characters & Online Roster
Route::middleware('module:character')->group(function () {
    Route::get('/characters/{name}', [\App\Http\Controllers\Public\CharacterViewController::class, 'show'])->name('characters.show');
    Route::get('/online', [\App\Http\Controllers\Public\CharacterViewController::class, 'online'])->name('characters.online');
});

// War of Emperium
Route::middleware('module:guild')->group(function () {
    Route::get('/woe', [\App\Http\Controllers\Public\WoeController::class, 'index'])->name('woe.index');
});

// Item Database
Route::middleware('module:itemdb')->group(function () {
    Route::get('/database/items', [ItemDbController::class, 'index'])->name('itemdb.index');
    Route::get('/database/items/{id}', [ItemDbController::class, 'show'])->name('itemdb.show');
});

// Downloads
Route::middleware('module:downloads')->group(function () {
    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
});

// Donations
Route::middleware('module:donations')->group(function () {
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::post('/donations/submit', [DonationController::class, 'submitManual'])->name('donations.submit')->middleware('auth');
    Route::post('/donations/paypal/checkout/{package}', [DonationController::class, 'checkoutPayPal'])->name('donations.paypal.checkout')->middleware('auth');
    Route::post('/donations/paypal/ipn', [DonationController::class, 'paypalIpn'])->name('donations.paypal.ipn');
    Route::get('/donations/paypal/success', [DonationController::class, 'paypalSuccess'])->name('donations.paypal.success');
    Route::get('/donations/paypal/cancel', [DonationController::class, 'paypalCancel'])->name('donations.paypal.cancel');
});

// Dynamic Theme Assets Route
Route::get('/themes/{theme}/assets/{path}', function (string $theme, string $path) {
    $filePath = base_path("themes/{$theme}/assets/{$path}");
    if (!file_exists($filePath) || str_contains($path, '..')) {
        abort(404);
    }
    $mime = match (pathinfo($filePath, PATHINFO_EXTENSION)) {
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'svg' => 'image/svg+xml',
        'woff2' => 'font/woff2',
        default => mime_content_type($filePath) ?: 'text/plain',
    };
    return response()->file($filePath, ['Content-Type' => $mime]);
})->where('path', '.*')->name('theme.asset');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::post('/password', [AccountController::class, 'changePassword'])->name('password');
        Route::post('/email', [AccountController::class, 'changeEmail'])->name('email');
        Route::post('/pincode', [AccountController::class, 'changePincode'])->name('pincode');
        Route::get('/characters', [AccountController::class, 'characters'])->name('characters');
        Route::post('/characters/{id}/unstuck', [AccountController::class, 'unstuck'])->name('unstuck');
        Route::delete('/characters/{id}', [AccountController::class, 'deleteCharacter'])->name('characters.delete');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Control Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Account Management
    Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts');
    Route::post('/accounts/{id}/ban', [AdminController::class, 'toggleBan'])->name('accounts.ban');

    // Character Management
    Route::get('/characters', [AdminController::class, 'characters'])->name('characters');

    // Guild Management
    Route::get('/guilds', [AdminController::class, 'guilds'])->name('guilds');

    // News & Content
    Route::get('/news', [AdminController::class, 'news'])->name('news.index');
    Route::get('/news/create', [AdminController::class, 'createNews'])->name('news.create');
    Route::post('/news', [AdminController::class, 'storeNews'])->name('news.store');
    Route::delete('/news/{id}', [AdminController::class, 'deleteNews'])->name('news.delete');

    // Donations
    Route::get('/donations', [AdminController::class, 'donations'])->name('donations');
    Route::post('/donations/{id}/status', [AdminController::class, 'updateDonationStatus'])->name('donations.status');
    Route::get('/donations/packages', [AdminController::class, 'donationPackages'])->name('donations.packages');
    Route::post('/donations/packages', [AdminController::class, 'storeDonationPackage'])->name('donations.packages.store');
    Route::put('/donations/packages/{id}', [AdminController::class, 'updateDonationPackage'])->name('donations.packages.update');
    Route::delete('/donations/packages/{id}', [AdminController::class, 'deleteDonationPackage'])->name('donations.packages.delete');
    Route::post('/donations/packages/{id}/toggle', [AdminController::class, 'toggleDonationPackage'])->name('donations.packages.toggle');
    Route::post('/donations/settings', [AdminController::class, 'updateDonationSettings'])->name('donations.settings.update');

    // Modules & Themes
    Route::get('/modules', [AdminController::class, 'modules'])->name('modules');
    Route::post('/modules/{key}/toggle', [AdminController::class, 'toggleModule'])->name('modules.toggle');
    Route::get('/themes', [AdminController::class, 'themes'])->name('themes');
    Route::post('/themes/{theme}/activate', [AdminController::class, 'activateTheme'])->name('themes.activate');

    // Database & YAML Importer
    Route::get('/importer', [AdminController::class, 'importer'])->name('importer');
    Route::post('/importer', [AdminController::class, 'runImport'])->name('importer.run');

    // Logs & Settings
    Route::get('/logs', [AdminController::class, 'activityLogs'])->name('logs');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Installation Wizard Routes
|--------------------------------------------------------------------------
*/
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Install\InstallController::class, 'index'])->name('index');
    Route::get('/requirements', [\App\Http\Controllers\Install\InstallController::class, 'checkRequirements'])->name('requirements');
    Route::post('/test-database', [\App\Http\Controllers\Install\InstallController::class, 'testDatabase'])->name('test-database');
    Route::post('/test-game-database', [\App\Http\Controllers\Install\InstallController::class, 'testGameDatabase'])->name('test-game-database');
    Route::post('/setup-environment', [\App\Http\Controllers\Install\InstallController::class, 'setupEnvironment'])->name('setup-environment');
    Route::post('/setup-admin', [\App\Http\Controllers\Install\InstallController::class, 'setupAdmin'])->name('setup-admin');
});

