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
    Route::get('/rankings/feed/{category}', [RankingController::class, 'feed'])->name('rankings.feed');
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

// Item, Monster, Map & Game Database Center
Route::middleware('module:database')->group(function () {
    Route::get('/database/items', [ItemDbController::class, 'index'])->name('itemdb.index');
    Route::get('/database', [ItemDbController::class, 'index'])->name('database.items');
    Route::get('/database/items/{id}', [ItemDbController::class, 'show'])->name('itemdb.show');
    Route::get('/database/monsters', [ItemDbController::class, 'monsters'])->name('database.monsters');
    Route::get('/database/monsters/{id}', [ItemDbController::class, 'showMonster'])->name('database.monsters.show');
    Route::get('/database/maps', [ItemDbController::class, 'maps'])->name('database.maps');
});

// Server Status & Demographics
Route::get('/server-status', [\App\Http\Controllers\Public\ServerStatusController::class, 'index'])->name('server.status');
Route::get('/status', [\App\Http\Controllers\Public\ServerStatusController::class, 'index'])->name('status');

// Downloads
Route::middleware('module:downloads')->group(function () {
    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
});

// Gallery & Screenshots
Route::middleware('module:gallery')->group(function () {
    Route::get('/gallery', [\App\Http\Controllers\Public\GalleryController::class, 'index'])->name('gallery.index');
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

// Player Marketplace
Route::middleware(['auth', 'module:marketplace'])->prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\MarketplaceController::class, 'index'])->name('index');
    Route::post('/character', [\App\Http\Controllers\Public\MarketplaceController::class, 'selectCharacter'])->name('character');
    Route::post('/transfer', [\App\Http\Controllers\Public\MarketplaceController::class, 'transfer'])->name('transfer');
    Route::post('/list', [\App\Http\Controllers\Public\MarketplaceController::class, 'createListing'])->name('list');
    Route::post('/cancel/{id}', [\App\Http\Controllers\Public\MarketplaceController::class, 'cancel'])->name('cancel');
    Route::post('/withdraw/{id}', [\App\Http\Controllers\Public\MarketplaceController::class, 'withdraw'])->name('withdraw');
    Route::post('/buy/{id}', [\App\Http\Controllers\Public\MarketplaceController::class, 'buy'])->name('buy');
});

// Support Center & Ticket Management
Route::middleware(['auth', 'module:support'])->prefix('support')->name('support.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\SupportController::class, 'index'])->name('index');
    Route::get('/my-tickets', [\App\Http\Controllers\Public\SupportController::class, 'myTickets'])->name('my-tickets');
    Route::get('/create', [\App\Http\Controllers\Public\SupportController::class, 'create'])->name('create');
    Route::post('/create', [\App\Http\Controllers\Public\SupportController::class, 'store'])->name('store');
    Route::get('/tickets/{reference}', [\App\Http\Controllers\Public\SupportController::class, 'show'])->name('show');
    Route::post('/tickets/{reference}/reply', [\App\Http\Controllers\Public\SupportController::class, 'reply'])->name('reply');
    Route::post('/tickets/{reference}/close', [\App\Http\Controllers\Public\SupportController::class, 'close'])->name('close');
    Route::post('/tickets/{reference}/reopen', [\App\Http\Controllers\Public\SupportController::class, 'reopen'])->name('reopen');
    Route::get('/tickets/{reference}/attachments/{attachmentId}', [\App\Http\Controllers\Public\SupportController::class, 'downloadAttachment'])->name('attachment');
});

// FAQ & Knowledge Base
Route::middleware('module:faq')->prefix('faq')->name('faq.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\FaqController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [\App\Http\Controllers\Public\FaqController::class, 'category'])->name('category');
    Route::get('/{slug}', [\App\Http\Controllers\Public\FaqController::class, 'show'])->name('show');
    Route::post('/{id}/feedback', [\App\Http\Controllers\Public\FaqController::class, 'feedback'])->name('feedback');
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
        Route::post('/characters/{id}/reset-position', [AccountController::class, 'unstuck'])->name('characters.reset-position');
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
    Route::post('/donations/grant', [AdminController::class, 'grantDonationReward'])->name('donations.grant');
    Route::get('/donations/packages', [AdminController::class, 'donationPackages'])->name('donations.packages');
    Route::post('/donations/packages', [AdminController::class, 'storeDonationPackage'])->name('donations.packages.store');
    Route::put('/donations/packages/{id}', [AdminController::class, 'updateDonationPackage'])->name('donations.packages.update');
    Route::delete('/donations/packages/{id}', [AdminController::class, 'deleteDonationPackage'])->name('donations.packages.delete');
    Route::post('/donations/packages/{id}/toggle', [AdminController::class, 'toggleDonationPackage'])->name('donations.packages.toggle');
    Route::post('/donations/settings', [AdminController::class, 'updateDonationSettings'])->name('donations.settings.update');
    Route::post('/donations/settings/reset', [AdminController::class, 'resetDefaultDonationSettings'])->name('donations.settings.reset');

    // Downloads Editor
    Route::get('/downloads', [AdminController::class, 'downloads'])->name('downloads.index');
    Route::post('/downloads', [AdminController::class, 'storeDownload'])->name('downloads.store');
    Route::put('/downloads/{id}', [AdminController::class, 'updateDownload'])->name('downloads.update');
    Route::delete('/downloads/{id}', [AdminController::class, 'deleteDownload'])->name('downloads.delete');
    Route::post('/downloads/{id}/toggle', [AdminController::class, 'toggleDownload'])->name('downloads.toggle');

    // Gallery Manager
    Route::get('/gallery', [\App\Http\Controllers\Admin\AdminGalleryController::class, 'index'])->name('gallery.index');
    Route::post('/gallery', [\App\Http\Controllers\Admin\AdminGalleryController::class, 'store'])->name('gallery.store');
    Route::put('/gallery/{id}', [\App\Http\Controllers\Admin\AdminGalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/gallery/{id}', [\App\Http\Controllers\Admin\AdminGalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::post('/gallery/reset', [\App\Http\Controllers\Admin\AdminGalleryController::class, 'resetDefaults'])->name('gallery.reset');

    // Marketplace Manager
    Route::get('/marketplace', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'index'])->name('marketplace.index');
    Route::post('/marketplace/settings', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'updateSettings'])->name('marketplace.settings');
    Route::post('/marketplace/cancel/{id}', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'cancelListing'])->name('marketplace.cancel');
    Route::post('/marketplace/moderate/{id}', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'cancelListing'])->name('marketplace.moderate');
    Route::post('/marketplace/return/{id}', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'returnItem'])->name('marketplace.return');
    Route::post('/marketplace/refund/{id}', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'returnItem'])->name('marketplace.custody.refund');

    // Support Center Management
    Route::get('/support', [\App\Http\Controllers\Admin\AdminSupportController::class, 'index'])->name('support.index');
    Route::get('/support/settings', [\App\Http\Controllers\Admin\AdminSupportController::class, 'settings'])->name('support.settings');
    Route::post('/support/settings', [\App\Http\Controllers\Admin\AdminSupportController::class, 'updateSettings'])->name('support.settings.update');
    Route::get('/support/tickets/{id}', [\App\Http\Controllers\Admin\AdminSupportController::class, 'show'])->name('support.show');
    Route::post('/support/tickets/{id}/reply', [\App\Http\Controllers\Admin\AdminSupportController::class, 'reply'])->name('support.reply');
    Route::post('/support/tickets/{id}/update', [\App\Http\Controllers\Admin\AdminSupportController::class, 'update'])->name('support.update');
    Route::get('/support/tickets/{id}/attachments/{attachmentId}', [\App\Http\Controllers\Admin\AdminSupportController::class, 'downloadAttachment'])->name('support.attachment');

    // FAQ Management
    Route::prefix('faq')->name('faq.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminFaqController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdminFaqController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AdminFaqController::class, 'store'])->name('store');
        Route::get('/categories', [\App\Http\Controllers\Admin\AdminFaqController::class, 'categories'])->name('categories');
        Route::post('/categories', [\App\Http\Controllers\Admin\AdminFaqController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [\App\Http\Controllers\Admin\AdminFaqController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\AdminFaqController::class, 'destroyCategory'])->name('categories.destroy');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AdminFaqController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AdminFaqController::class, 'update'])->name('update');
        Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\AdminFaqController::class, 'toggleStatus'])->name('toggle');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AdminFaqController::class, 'destroy'])->name('destroy');
    });

    // Item Database Search for Admin Selectors
    Route::get('/items/search', [AdminController::class, 'searchItems'])->name('items.search');

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

    // Ranking Engine & Synchronization Management
    Route::get('/rankings', [\App\Http\Controllers\Admin\AdminRankingController::class, 'index'])->name('rankings.index');
    Route::post('/rankings/settings', [\App\Http\Controllers\Admin\AdminRankingController::class, 'updateSettings'])->name('rankings.settings');
    Route::post('/rankings/sync', [\App\Http\Controllers\Admin\AdminRankingController::class, 'syncNow'])->name('rankings.sync');
    Route::post('/rankings/token', [\App\Http\Controllers\Admin\AdminRankingController::class, 'regenerateToken'])->name('rankings.token');

    // Badge & Tier Management
    Route::get('/badges', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'index'])->name('badges.index');
    Route::post('/badges', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'store'])->name('badges.store');
    Route::put('/badges/{id}', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'update'])->name('badges.update');
    Route::post('/badges/{id}/toggle', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'toggle'])->name('badges.toggle');
    Route::post('/badges/tiers', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'updateTiers'])->name('badges.tiers');
    Route::post('/badges/assign', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'assign'])->name('badges.assign');
    Route::post('/badges/assignments/{id}/revoke', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'revoke'])->name('badges.revoke');
    Route::post('/badges/recalculate', [\App\Http\Controllers\Admin\AdminBadgeController::class, 'recalculate'])->name('badges.recalculate');

    // Milestone Reward System
    Route::get('/rewards', [\App\Http\Controllers\Admin\AdminRewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards', [\App\Http\Controllers\Admin\AdminRewardController::class, 'store'])->name('rewards.store');
    Route::post('/rewards/{id}/toggle', [\App\Http\Controllers\Admin\AdminRewardController::class, 'toggle'])->name('rewards.toggle');
    Route::post('/rewards/issuances/{id}/retry', [\App\Http\Controllers\Admin\AdminRewardController::class, 'retry'])->name('rewards.retry');
});

/*
|--------------------------------------------------------------------------
| Authenticated Emulator Event Ingestion Bridge
|--------------------------------------------------------------------------
*/
Route::prefix('api/emulator')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/events', [\App\Http\Controllers\Api\EmulatorEventController::class, 'store'])->name('api.emulator.events.store');
    Route::post('/events/batch', [\App\Http\Controllers\Api\EmulatorEventController::class, 'batch'])->name('api.emulator.events.batch');
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

