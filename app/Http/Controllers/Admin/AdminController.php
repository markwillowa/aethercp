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
        $newState = $moduleManager->toggle($key);
        $migrationNote = $moduleManager->getLastMigrationNote();
        $stateLabel = $newState ? 'enabled' : 'disabled';

        ActivityLog::log('admin.module_toggle', "Toggled module [{$key}] ({$stateLabel})." . ($migrationNote ? " {$migrationNote}" : ''));

        $message = "Module [{$key}] {$stateLabel}.";
        if ($migrationNote) {
            $message .= ' ' . $migrationNote;
        }

        return back()->with('success', $message);
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
        $mobDbCount = 0;
        $mobDb2Count = 0;
        $tableExists = false;
        $availableTables = [];
        $databaseName = config('database.connections.ragnarok.database', 'ragnarok');

        try {
            if (\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('item_db')) {
                $tableExists = true;
                $itemDbCount = \Illuminate\Support\Facades\DB::connection('ragnarok')->table('item_db')->count();
            }
            if (\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('item_db2')) {
                $itemDb2Count = \Illuminate\Support\Facades\DB::connection('ragnarok')->table('item_db2')->count();
            }
            if (\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('mob_db')) {
                $mobDbCount = \Illuminate\Support\Facades\DB::connection('ragnarok')->table('mob_db')->count();
            }
            if (\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('mob_db2')) {
                $mobDb2Count = \Illuminate\Support\Facades\DB::connection('ragnarok')->table('mob_db2')->count();
            }

            $dbName = \Illuminate\Support\Facades\DB::connection('ragnarok')->getDatabaseName();
            if ($dbName) {
                $databaseName = $dbName;
            }

            // Query all available tables on the server connection with approximate row counts
            $rawTables = \Illuminate\Support\Facades\DB::connection('ragnarok')->select("
                SELECT TABLE_NAME as name, TABLE_ROWS as row_count 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = ? 
                ORDER BY TABLE_NAME ASC
            ", [$databaseName]);

            foreach ($rawTables as $r) {
                $availableTables[] = [
                    'name' => $r->name,
                    'rows' => (int) ($r->row_count ?? 0),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback to Schema table listing if information_schema query fails or in testing
            try {
                $tableNames = \Illuminate\Support\Facades\Schema::connection('ragnarok')->getTableListing();
                sort($tableNames);
                foreach ($tableNames as $t) {
                    $availableTables[] = [
                        'name' => $t,
                        'rows' => 0,
                    ];
                }
            } catch (\Throwable $e2) {
                $availableTables = [
                    ['name' => 'item_db', 'rows' => 0],
                    ['name' => 'item_db2', 'rows' => 0],
                ];
            }
        }

        if (empty($availableTables)) {
            $availableTables = [
                ['name' => 'item_db', 'rows' => 0],
                ['name' => 'item_db2', 'rows' => 0],
            ];
        }

        $totalTablesCount = count($availableTables);

        // Search for known local emulator YAML paths
        $suggestedPaths = [];
        $candidatePaths = [
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_equip.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_usable.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/item_db_etc.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/item_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/item_db_equip.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/pre-re/mob_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/re/mob_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/mob_db.yml',
            'C:/Users/villa/Desktop/FreinyxRO/rathena/db/import/mob_db.yml',
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

        return view('admin.importer', compact('itemDbCount', 'itemDb2Count', 'mobDbCount', 'mobDb2Count', 'tableExists', 'suggestedPaths', 'availableTables', 'totalTablesCount', 'databaseName'));
    }

    public function runImport(Request $request, \App\Services\Database\ItemDbImporter $importer)
    {
        $request->validate([
            'source_type' => 'required|in:upload,path,url',
            'target_table' => 'required|string|regex:/^[a-zA-Z0-9_]+$/',
            'mode' => 'required|in:upsert,truncate,insert_missing',
            'yaml_file' => 'required_if:source_type,upload|file|max:51200',
            'file_path' => 'required_if:source_type,path|nullable|string',
            'remote_url' => 'required_if:source_type,url|nullable|url',
        ]);

        $sourceType = $request->input('source_type');
        $targetTable = $request->input('target_table');
        $mode = $request->input('mode');
        $tempFile = null;

        if (!\Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable($targetTable)) {
            return back()->with('error', "Target table [{$targetTable}] does not exist on the database server.");
        }

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

    public static function getDefaultDonationGuides(): array
    {
        $serverName = config('aether.server_name', 'FreynixRO');
        return [
            'donation_instructions' => "Welcome to the official {$serverName} Support & Donation Portal!\n\nAll contributions go directly toward sustaining our high-performance dedicated server hosting, automated daily off-site backups, DDoS mitigation, anti-cheat licensing, and ongoing episode content expansions.\n\n⚡ Instant Automated Delivery:\nDonations completed via PayPal are automatically processed via IPN within seconds. Your Cash Points are immediately credited to your account (#CASHPOINTS) and reward items are placed into your account Kafra Storage.\n\n🛡️ Alternative & Manual Payment:\nIf you contribute via Bank Wire, GCash, or Crypto, submit your transaction reference ID below for Game Master verification.",

            'donation_payment_details' => "Accepted Contribution Channels:\n\n1. Instant PayPal Checkout:\nClick the 'Instant PayPal Checkout' button on any package tier above for immediate automated delivery.\n\n2. G-Cash / E-Wallet:\nAccount Name: {$serverName} Administration\nAccount Number: 0917-555-ROCP (0917-555-7627)\n\n3. Bank Wire / Online Deposit:\nBank Name: Banco de Oro (BDO)\nAccount Name: {$serverName} Operations\nAccount Number: 0048-2819-9401\n\n4. Cryptocurrency (USDT - TRC20):\nWallet: TFreynixROCommunityWalletAddress7788\nNetwork: Tron (TRC20)\n\n*Important: After completing an offline transfer, submit your Transaction Reference ID below for GM verification.",

            'donation_kafra_guide' => "How to Retrieve Your In-Game Rewards:\n\n1. Cash Points (#CASHPOINTS):\nPoints are automatically attached to your master account. You can verify your balance in-game using the @cashpoints command or by clicking the Cash Shop icon below the mini-map.\n\n2. In-Game Item Rewards (Kafra Storage Vault):\nReward items are delivered directly into your account's shared Kafra Storage.\n• Step 1: Log into any character on your account.\n• Step 2: Speak with any Kafra Employee in Prontera (Central: prontera 151, 187), Morroc, Geffen, Payon, or Alberta.\n• Step 3: Select 'Use Storage' from the menu.\n• Step 4: Your bonus reward items are ready to be withdrawn!",

            'donation_terms' => "Donation Terms & Voluntary Support Policy:\n1. All donations are 100% voluntary contributions made to support {$serverName} server infrastructure and uptime.\n2. In-game Cash Points and bonus storage items are complimentary digital tokens of appreciation.\n3. Donations are non-refundable once processed and delivered.\n4. Game server rules apply equally to all adventurers; donor status provides no exemption from gameplay rules or anti-cheat policies.\n5. For support or payment inquiries, open a ticket on our official Discord community.",
        ];
    }

    public function donationPackages()
    {
        $packages = DonationPackage::orderBy('sort_order', 'asc')->get();

        // Check if donation guides exist; if not, seed authentic defaults
        if (!SiteSetting::where('key', 'donation_instructions')->exists()) {
            foreach (static::getDefaultDonationGuides() as $k => $v) {
                SiteSetting::set($k, $v, 'donations');
            }
        }

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

        if (!empty($itemId)) {
            $item = $itemRepo->findById((int) $itemId);
            if (!$item) {
                return back()->withInput()->with('error', "Item ID {$itemId} does not exist in the game database. Only items existing in the database can be selected as rewards.");
            }
            if (empty($itemName)) {
                $itemName = $item->name_english ?: ($item->name_aegis ?: "Item #{$itemId}");
            }
        } else {
            $itemId = null;
            $itemName = null;
        }

        $package = DonationPackage::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'currency' => strtoupper($request->input('currency', 'USD')),
            'points' => (int) $request->input('points'),
            'bonus_points' => (int) $request->input('bonus_points', 0),
            'item_id' => $itemId,
            'item_amount' => $itemId ? (int) $request->input('item_amount', 1) : 0,
            'item_name' => $itemName,
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

        if (!empty($itemId)) {
            $item = $itemRepo->findById((int) $itemId);
            if (!$item) {
                return back()->withInput()->with('error', "Item ID {$itemId} does not exist in the game database. Only items existing in the database can be selected as rewards.");
            }
            if (empty($itemName)) {
                $itemName = $item->name_english ?: ($item->name_aegis ?: "Item #{$itemId}");
            }
        } else {
            $itemId = null;
            $itemName = null;
        }

        $package->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'currency' => strtoupper($request->input('currency', 'USD')),
            'points' => (int) $request->input('points'),
            'bonus_points' => (int) $request->input('bonus_points', 0),
            'item_id' => $itemId,
            'item_amount' => $itemId ? (int) $request->input('item_amount', 1) : 0,
            'item_name' => $itemName,
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

    public function resetDefaultDonationSettings()
    {
        foreach (static::getDefaultDonationGuides() as $k => $v) {
            SiteSetting::set($k, $v, 'donations');
        }

        ActivityLog::log('admin.donation_guides_reset', "Reset donation guides and instructions to authentic defaults.");

        return back()->with('success', 'Donation guides, payment instructions, and Kafra delivery details have been populated with authentic defaults.');
    }

    public function searchItems(Request $request, \App\Contracts\Repositories\ItemRepositoryInterface $itemRepo)
    {
        $keyword = trim($request->input('q', ''));
        if (strlen($keyword) < 1) {
            return response()->json([]);
        }

        $res = $itemRepo->search($keyword, null, 15);
        $formatted = array_map(function ($item) {
            $name = $item->name_english ?? $item->name_aegis ?? "Item #{$item->id}";
            return [
                'id' => $item->id,
                'name' => $name,
                'aegis' => $item->name_aegis ?? '',
                'type' => $item->type ?? 'Item',
                'slots' => $item->slots ?? 0,
            ];
        }, $res['items'] ?? []);

        return response()->json($formatted);
    }

    public function grantDonationReward(
        Request $request,
        \App\Services\Donation\InGameRewardService $rewardService,
        \App\Contracts\Repositories\ItemRepositoryInterface $itemRepo
    ) {
        $request->validate([
            'identifier' => 'required|string|max:50',
            'points' => 'nullable|integer|min:0',
            'item_id' => 'nullable|integer|min:1',
            'item_amount' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $identifier = trim($request->input('identifier'));
        $points = (int) $request->input('points', 0);
        $itemId = $request->input('item_id') ? (int) $request->input('item_id') : null;
        $itemAmount = $itemId ? (int) $request->input('item_amount', 1) : 0;
        $notes = $request->input('notes') ?: 'Direct GM Reward Grant';

        if ($points <= 0 && empty($itemId)) {
            return back()->withInput()->with('error', 'Please specify either Cash Points or an Item Reward to grant.');
        }

        // Find game account by account_id or userid
        $account = is_numeric($identifier)
            ? $this->accounts->findById((int) $identifier)
            : $this->accounts->findByUsername($identifier);

        if (!$account) {
            return back()->withInput()->with('error', "Game account '{$identifier}' could not be found.");
        }

        $itemName = null;
        if ($itemId) {
            $item = $itemRepo->findById($itemId);
            if (!$item) {
                return back()->withInput()->with('error', "Item ID {$itemId} does not exist in the game database.");
            }
            $itemName = $item->name_english ?: ($item->name_aegis ?: "Item #{$itemId}");
        }

        $noteDetails = $notes . ($itemId ? " | Item: {$itemAmount}x [{$itemName}] (#{$itemId})" : "");

        // Create an approved donation ledger record
        $donation = Donation::create([
            'account_id' => $account->account_id,
            'package_id' => null,
            'amount' => 0.00,
            'currency' => 'USD',
            'points_awarded' => $points,
            'payment_method' => 'admin_grant',
            'transaction_reference' => 'GM-GRANT-' . strtoupper(bin2hex(random_bytes(6))),
            'status' => 'approved',
            'notes' => $noteDetails . ' (Granted by GM ' . (auth()->user()->username ?? 'Admin') . ')',
            'points_delivered' => false,
            'items_delivered' => false,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $deliveryMessages = [];

        // Deliver Cash Points directly into acc_reg_num (#CASHPOINTS)
        if ($points > 0) {
            $success = $rewardService->creditCashPoints($account->account_id, $points);
            if ($success) {
                $donation->points_delivered = true;
                $deliveryMessages[] = number_format($points) . " Cash Points";
            }
        }

        // Deliver Item directly into player in-game storage
        if ($itemId && $itemAmount > 0) {
            $success = $rewardService->deliverItemToStorage($account->account_id, $itemId, $itemAmount);
            if ($success) {
                $donation->items_delivered = true;
                $deliveryMessages[] = "{$itemAmount}x [{$itemName}] (ID: {$itemId}) delivered to Storage";
            }
        }

        $donation->save();

        $summary = implode(' and ', $deliveryMessages);
        ActivityLog::log('admin.reward_grant', "Granted {$summary} to Account #{$account->account_id} ({$account->userid})");

        return back()->with('success', "Successfully granted {$summary} to game account {$account->userid} (ID: #{$account->account_id})!");
    }

    public function downloads()
    {
        $downloads = Download::orderBy('sort_order')->orderBy('id', 'desc')->get();
        return view('admin.downloads', compact('downloads'));
    }

    public function storeDownload(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'version' => 'nullable|string|max:20',
            'file_size' => 'nullable|string|max:50',
            'url_mirror1' => 'required|url|max:255',
            'url_mirror2' => 'nullable|url|max:255',
            'url_mirror3' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $download = Download::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'version' => $validated['version'] ?? null,
            'file_size' => $validated['file_size'] ?? null,
            'url_mirror1' => $validated['url_mirror1'],
            'url_mirror2' => $validated['url_mirror2'] ?? null,
            'url_mirror3' => $validated['url_mirror3'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) $request->input('is_active', true),
        ]);

        ActivityLog::log('admin.download_create', "Added new game client download [{$download->title}]");

        return back()->with('success', "Download '{$download->title}' added successfully.");
    }

    public function updateDownload(Request $request, int $id)
    {
        $download = Download::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'version' => 'nullable|string|max:20',
            'file_size' => 'nullable|string|max:50',
            'url_mirror1' => 'required|url|max:255',
            'url_mirror2' => 'nullable|url|max:255',
            'url_mirror3' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $download->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'version' => $validated['version'] ?? null,
            'file_size' => $validated['file_size'] ?? null,
            'url_mirror1' => $validated['url_mirror1'],
            'url_mirror2' => $validated['url_mirror2'] ?? null,
            'url_mirror3' => $validated['url_mirror3'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) $request->input('is_active', true),
        ]);

        ActivityLog::log('admin.download_update', "Updated game client download [{$download->title}]");

        return back()->with('success', "Download '{$download->title}' updated successfully.");
    }

    public function deleteDownload(int $id)
    {
        $download = Download::findOrFail($id);
        $title = $download->title;
        $download->delete();

        ActivityLog::log('admin.download_delete', "Deleted game download package [{$title}]");

        return back()->with('success', "Download '{$title}' deleted successfully.");
    }

    public function toggleDownload(int $id)
    {
        $download = Download::findOrFail($id);
        $download->is_active = !$download->is_active;
        $download->save();

        ActivityLog::log('admin.download_toggle', "Toggled status for download [{$download->title}] to " . ($download->is_active ? 'active' : 'inactive'));

        return back()->with('success', "Download status updated.");
    }
}
