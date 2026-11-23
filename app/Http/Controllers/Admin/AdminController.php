<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\AccountRepositoryInterface;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Contracts\EmulatorInterface;
use App\Models\User;
use App\Models\News;
use App\Models\Download;
use App\Models\Donation;
use App\Models\DonationPackage;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct(
        protected AccountRepositoryInterface $accounts,
        protected CharacterRepositoryInterface $characters,
        protected GuildRepositoryInterface $guilds,
        protected EmulatorInterface $emulator
    ) {}

    public function dashboard()
    {
        $totalWebUsers = User::count();
        $totalNews = News::count();
        $totalDownloads = Download::count();
        $pendingDonations = Donation::where('status', 'pending')->count();
        $onlinePlayers = $this->emulator->getOnlinePlayerCount();

        $recentLogs = ActivityLog::latest()->limit(8)->get();
        $recentUsers = User::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalWebUsers',
            'totalNews',
            'totalDownloads',
            'pendingDonations',
            'onlinePlayers',
            'recentLogs',
            'recentUsers'
        ));
    }

    public function accounts(Request $request)
    {
        $search = trim($request->query('q', ''));
        $loginTable = $this->emulator->getTableName('login');

        $query = DB::connection('ragnarok')->table($loginTable);

        if (!empty($search)) {
            if (is_numeric($search)) {
                $query->where('account_id', (int) $search);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('userid', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
        }

        $gameAccounts = $query->orderBy('account_id', 'desc')->paginate(20);

        return view('admin.accounts', compact('gameAccounts', 'search'));
    }

    public function toggleBan(Request $request, int $accountId)
    {
        $account = $this->accounts->findById($accountId);
        if (!$account) {
            return back()->with('error', 'Account not found.');
        }

        $newState = ($account->state > 0) ? 0 : 5; // 0 = Active, 5 = Banned
        $unbanTime = ($newState > 0) ? (time() + 86400 * 30) : 0; // 30 days ban or unban

        $this->accounts->setAccountState($accountId, $newState, $unbanTime);

        // Also update local web user
        User::where('account_id', $accountId)->update(['is_active' => ($newState === 0)]);

        $action = ($newState > 0) ? 'Banned' : 'Unbanned';
        ActivityLog::log('admin.ban_toggle', "{$action} account #{$accountId} ({$account->userid})", $accountId);

        return back()->with('success', "Account #{$accountId} ({$account->userid}) has been {$action}.");
    }

    public function characters(Request $request)
    {
        $search = trim($request->query('q', ''));
        $charTable = $this->emulator->getTableName('char');

        $query = DB::connection('ragnarok')->table(DB::raw($this->emulator->quoteIdentifier($charTable)));

        if (!empty($search)) {
            if (is_numeric($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('char_id', (int) $search)->orWhere('account_id', (int) $search);
                });
            } else {
                $query->where('name', 'like', "%{$search}%");
            }
        }

        $characters = $query->orderBy('char_id', 'desc')->paginate(20);

        return view('admin.characters', compact('characters', 'search'));
    }

    public function guilds(Request $request)
    {
        $search = trim($request->query('q', ''));
        $guildTable = $this->emulator->getTableName('guild');

        $query = DB::connection('ragnarok')->table($guildTable);

        if (!empty($search)) {
            if (is_numeric($search)) {
                $query->where('guild_id', (int) $search);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('master', 'like', "%{$search}%");
                });
            }
        }

        $guilds = $query->orderBy('guild_lv', 'desc')->paginate(20);
        $castles = $this->guilds->getCastles();

        return view('admin.guilds', compact('guilds', 'castles', 'search'));
    }

    public function news()
    {
        $articles = News::latest()->paginate(15);
        return view('admin.news.index', compact('articles'));
    }

    public function createNews()
    {
        return view('admin.news.create');
    }

    public function storeNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|in:news,announcement,event,update,maintenance',
            'summary' => 'nullable|string|max:300',
            'content' => 'required|string',
            'is_pinned' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        News::create([
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'author_id' => auth()->id(),
            'author_name' => auth()->user()->username ?? 'Admin',
            'is_pinned' => (bool) $request->input('is_pinned', false),
            'is_published' => (bool) $request->input('is_published', true),
            'published_at' => now(),
        ]);

        ActivityLog::log('admin.news_create', "Created news post: {$request->input('title')}");

        return redirect()->route('admin.news.index')->with('success', 'News article published successfully.');
    }

    public function deleteNews(int $id)
    {
        $news = News::findOrFail($id);
        $title = $news->title;
        $news->delete();

        ActivityLog::log('admin.news_delete', "Deleted news article: {$title}");

        return back()->with('success', "Article '{$title}' deleted.");
    }

    public function donations()
    {
        $donations = Donation::with('package')->latest()->paginate(20);
        return view('admin.donations', compact('donations'));
    }

    public function updateDonationStatus(Request $request, int $id, \App\Services\Donation\InGameRewardService $rewardService)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $donation = Donation::findOrFail($id);
        $newStatus = $request->input('status');

        $donation->update([
            'status' => $newStatus,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $rewardMsg = '';
        if ($newStatus === 'approved') {
            $fulfillment = $rewardService->fulfillDonation($donation);
            if (!empty($fulfillment['messages'])) {
                $rewardMsg = ' ' . implode(' ', $fulfillment['messages']);
            }
        }

        ActivityLog::log('admin.donation_status', "Updated donation #{$id} status to {$newStatus}.{$rewardMsg}", $donation->account_id);

        return back()->with('success', "Donation transaction #{$id} updated to {$newStatus}.{$rewardMsg}");
    }

    public function activityLogs()
    {
        $logs = ActivityLog::latest()->paginate(30);
        return view('admin.logs', compact('logs'));
    }

    public function settings()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        ActivityLog::log('admin.settings_update', 'Updated server and website settings.');

        return back()->with('success', 'Site settings saved successfully.');
    }

    public function modules(\App\Services\Module\ModuleManager $moduleManager)
    {
        $modules = $moduleManager->getModules();
        return view('admin.modules', compact('modules'));
    }

    public function toggleModule(\App\Services\Module\ModuleManager $moduleManager, string $key)
    {
        $res = $moduleManager->toggle($key);
        ActivityLog::log('admin.module_toggle', "Toggled module [{$key}].");

        return back()->with('success', "Module [{$key}] state updated.");
    }

    public function themes(\App\Services\Theme\ThemeManager $themeManager)
    {
        $themes = $themeManager->getInstalledThemes();
        $activeTheme = $themeManager->getActiveTheme();
        return view('admin.themes', compact('themes', 'activeTheme'));
    }

    public function activateTheme(\App\Services\Theme\ThemeManager $themeManager, string $theme)
    {
        if ($themeManager->setActiveTheme($theme)) {
            ActivityLog::log('admin.theme_activate', "Activated theme [{$theme}].");
            return back()->with('success', "Theme [{$theme}] activated.");
        }

        return back()->with('error', "Theme [{$theme}] could not be found.");
    }

    public function importer()
    {
        $itemDbCount = 0;
        $itemDb2Count = 0;
        $tableExists = false;

        try {
            if (\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('item_db')) {
                $tableExists = true;
                $itemDbCount = \Illuminate\Support\Facades\DB::connection('ragnarok')->table('item_db')->count();
            }
            if (\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('item_db2')) {
                $itemDb2Count = \Illuminate\Support\Facades\DB::connection('ragnarok')->table('item_db2')->count();
            }
        } catch (\Throwable $e) {
            // Ignore connection error for isolated testing
        }

        // Search for known local emulator YAML paths
        $suggestedPaths = [];
        $candidatePaths = [
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_equip.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_usable.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_etc.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/item_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/item_db_equip.yml',
            base_path('database/data/item_db.yml'),
        ];

        foreach ($candidatePaths as $p) {
            if (file_exists($p) && is_readable($p)) {
                $suggestedPaths[] = [
                    'path' => $p,
                    'size' => round(filesize($p) / 1024, 1) . ' KB',
                    'filename' => basename($p),
                ];
            }
        }

        return view('admin.importer', compact('itemDbCount', 'itemDb2Count', 'tableExists', 'suggestedPaths'));
    }

    public function runImport(Request $request, \App\Services\Database\ItemDbImporter $importer)
    {
        $request->validate([
            'source_type' => 'required|in:upload,path,url',
            'target_table' => 'required|in:item_db,item_db2',
            'mode' => 'required|in:upsert,truncate,insert_missing',
            'yaml_file' => 'required_if:source_type,upload|file|max:51200',
            'file_path' => 'required_if:source_type,path|nullable|string',
            'remote_url' => 'required_if:source_type,url|nullable|url',
        ]);

        $sourceType = $request->input('source_type');
        $targetTable = $request->input('target_table');
        $mode = $request->input('mode');
        $tempFile = null;

        try {
            if ($sourceType === 'upload') {
                $file = $request->file('yaml_file');
                if (!$file || !$file->isValid()) {
                    return back()->with('error', 'Uploaded YAML file is invalid or missing.');
                }
                $filePath = $file->getRealPath();
            } elseif ($sourceType === 'path') {
                $filePath = trim($request->input('file_path'));
                if (!file_exists($filePath) || !is_readable($filePath)) {
                    return back()->with('error', "Server file path does not exist or cannot be read: {$filePath}");
                }
            } elseif ($sourceType === 'url') {
                $url = trim($request->input('remote_url'));
                $response = \Illuminate\Support\Facades\Http::timeout(60)->get($url);
                if (!$response->successful()) {
                    return back()->with('error', "Failed downloading remote YAML (HTTP {$response->status()}).");
                }
                $tempFile = tempnam(sys_get_temp_dir(), 'aether_yaml_');
                file_put_contents($tempFile, $response->body());
                $filePath = $tempFile;
            } else {
                return back()->with('error', 'Invalid source type specified.');
            }

            $result = $importer->import($filePath, $targetTable, $mode);

            $msg = "Successfully ingested {$result['imported']} items into [{$targetTable}] in {$result['elapsed_seconds']}s.";
            return back()->with('success', $msg)->with('import_result', $result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('YAML Importer Error: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        } finally {
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    }

    public function donationPackages()
    {
        $packages = DonationPackage::orderBy('sort_order', 'asc')->get();
        $instructions = SiteSetting::get('donation_instructions', '');
        $paymentDetails = SiteSetting::get('donation_payment_details', '');
        $kafraGuide = SiteSetting::get('donation_kafra_guide', '');
        $terms = SiteSetting::get('donation_terms', '');

        return view('admin.donation_packages', compact('packages', 'instructions', 'paymentDetails', 'kafraGuide', 'terms'));
    }

    public function storeDonationPackage(Request $request, \App\Contracts\Repositories\ItemRepositoryInterface $itemRepo)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'points' => 'required|integer|min:0',
            'bonus_points' => 'nullable|integer|min:0',
            'item_id' => 'nullable|integer|min:1',
            'item_amount' => 'nullable|integer|min:0',
            'item_name' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $itemId = $request->input('item_id');
        $itemName = $request->input('item_name');

        if ($itemId && empty($itemName)) {
            $item = $itemRepo->findById((int) $itemId);
            $itemName = $item ? ($item->name_english ?: $item->name_aegis) : "Reward Item #{$itemId}";
        }

        $package = DonationPackage::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'currency' => strtoupper($request->input('currency', 'USD')),
            'points' => (int) $request->input('points'),
            'bonus_points' => (int) $request->input('bonus_points', 0),
            'item_id' => $itemId ?: null,
            'item_amount' => (int) $request->input('item_amount', 0),
            'item_name' => $itemName ?: null,
            'is_popular' => (bool) $request->input('is_popular', false),
            'is_active' => (bool) $request->input('is_active', true),
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        ActivityLog::log('admin.package_create', "Created donation package [{$package->name}] (#{$package->id})");

        return back()->with('success', "Donation package '{$package->name}' created successfully.");
    }

    public function updateDonationPackage(Request $request, int $id, \App\Contracts\Repositories\ItemRepositoryInterface $itemRepo)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'points' => 'required|integer|min:0',
            'bonus_points' => 'nullable|integer|min:0',
            'item_id' => 'nullable|integer|min:1',
            'item_amount' => 'nullable|integer|min:0',
            'item_name' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $package = DonationPackage::findOrFail($id);
        $itemId = $request->input('item_id');
        $itemName = $request->input('item_name');

        if ($itemId && empty($itemName)) {
            $item = $itemRepo->findById((int) $itemId);
            $itemName = $item ? ($item->name_english ?: $item->name_aegis) : "Reward Item #{$itemId}";
        }

        $package->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'currency' => strtoupper($request->input('currency', 'USD')),
            'points' => (int) $request->input('points'),
            'bonus_points' => (int) $request->input('bonus_points', 0),
            'item_id' => $itemId ?: null,
            'item_amount' => (int) $request->input('item_amount', 0),
            'item_name' => $itemName ?: null,
            'is_popular' => (bool) $request->input('is_popular', false),
            'is_active' => (bool) $request->input('is_active', true),
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        ActivityLog::log('admin.package_update', "Updated donation package [{$package->name}] (#{$package->id})");

        return back()->with('success', "Donation package '{$package->name}' updated successfully.");
    }

    public function deleteDonationPackage(int $id)
    {
        $package = DonationPackage::findOrFail($id);
        $name = $package->name;
        $package->delete();

        ActivityLog::log('admin.package_delete', "Deleted donation package [{$name}] (#{$id})");

        return back()->with('success', "Donation package '{$name}' deleted successfully.");
    }

    public function toggleDonationPackage(int $id)
    {
        $package = DonationPackage::findOrFail($id);
        $package->is_active = !$package->is_active;
        $package->save();

        ActivityLog::log('admin.package_toggle', "Toggled status for package [{$package->name}] to " . ($package->is_active ? 'active' : 'inactive'));

        return back()->with('success', "Package '{$package->name}' status updated.");
    }

    public function updateDonationSettings(Request $request)
    {
        $instructions = $request->input('donation_instructions', '');
        $paymentDetails = $request->input('donation_payment_details', '');
        $kafraGuide = $request->input('donation_kafra_guide', '');
        $terms = $request->input('donation_terms', '');

        SiteSetting::set('donation_instructions', $instructions, 'donations');
        SiteSetting::set('donation_payment_details', $paymentDetails, 'donations');
        SiteSetting::set('donation_kafra_guide', $kafraGuide, 'donations');
        SiteSetting::set('donation_terms', $terms, 'donations');

        ActivityLog::log('admin.donation_settings', "Updated donation guidelines, instructions, and Kafra delivery details.");

        return back()->with('success', 'Donation instructions and reward guidelines saved successfully.');
    }
}
