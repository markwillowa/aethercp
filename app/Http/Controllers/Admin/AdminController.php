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

    public function updateDonationStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $donation = Donation::findOrFail($id);
        $donation->update([
            'status' => $request->input('status'),
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        ActivityLog::log('admin.donation_status', "Updated donation #{$id} status to {$request->input('status')}", $donation->account_id);

        return back()->with('success', "Donation transaction #{$id} updated to {$request->input('status')}.");
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
}
