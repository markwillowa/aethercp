<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;

$settings = SiteSetting::where('key', 'like', '%chronicles%')->get();
echo "Current chronicles settings in DB:\n";
foreach ($settings as $s) {
    echo "- {$s->key} = {$s->value}\n";
}

// If chronicles_bottom_mascot_img is set to a jpg or angeling_avatar.png, ensure it points to images/angeling_transparent.png
$mascot = SiteSetting::get('chronicles_bottom_mascot_img');
if (!empty($mascot) && (str_contains($mascot, '.jpg') || str_contains($mascot, 'angeling_avatar'))) {
    SiteSetting::set('chronicles_bottom_mascot_img', 'images/angeling_transparent.png');
    echo "Updated chronicles_bottom_mascot_img to images/angeling_transparent.png\n";
}

$badge = SiteSetting::get('chronicles_angeling_img');
if (!empty($badge) && str_contains($badge, '.jpg')) {
    SiteSetting::set('chronicles_angeling_img', 'images/angeling_transparent.png');
    echo "Updated chronicles_angeling_img to images/angeling_transparent.png\n";
}

// Update screenshot url to /gallery if empty or /news
$sUrl = SiteSetting::get('chronicles_screenshot_url');
if (empty($sUrl) || $sUrl === '/news') {
    SiteSetting::set('chronicles_screenshot_url', '/gallery');
    echo "Updated chronicles_screenshot_url to /gallery\n";
}

echo "Database sync done!\n";
