<?php

namespace App\Http\Controllers\Public;

use App\Contracts\Repositories\AccountRepositoryInterface;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Character\CharacterCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class CharacterCreationController extends Controller
{
    public function __construct(
        protected CharacterCreationService $creationService,
        protected CharacterRepositoryInterface $characterRepo,
        protected AccountRepositoryInterface $accountRepo
    ) {}

    public function create()
    {
        $user = Auth::user();
        $settings = $this->creationService->getSettings();

        if (!$settings['enabled']) {
            return redirect()->route('account.characters')
                ->with('error', 'Online Character Creation is currently disabled by the server administrator.');
        }

        $availableJobs = $this->creationService->getAvailableJobs($settings);
        $hairColors = $this->creationService->getHairColorCatalog();

        $existingCount = 0;
        $accountSex = 'M';
        $accountMaxSlots = $settings['max_slots'];

        if (!empty($user->account_id)) {
            $existingCount = count($this->characterRepo->getCharactersByAccount((int) $user->account_id));
            $roAccount = $this->accountRepo->findById((int) $user->account_id);
            if ($roAccount) {
                $accountSex = strtoupper((string) ($roAccount->sex ?? 'M'));
                $accountMaxSlots = min($settings['max_slots'], max(1, (int) ($roAccount->character_slots ?? $settings['max_slots'])));
            }
        }

        $canChangeAccountGender = (bool) $settings['enable_gender'];

        return view('account.create-character', compact(
            'user',
            'settings',
            'availableJobs',
            'hairColors',
            'existingCount',
            'accountSex',
            'accountMaxSlots',
            'canChangeAccountGender'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $result = $this->creationService->createCharacter($user, $request->all());

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        return redirect()->route('account.characters')->with('success', $result['message']);
    }

    /**
     * Cached proxy for external RO character sprite preview (prevents browser rate-limiting/bans).
     */
    public function spritePreview(Request $request)
    {
        $job = max(0, (int) $request->query('job', 0));
        $gender = strtoupper((string) $request->query('gender', 'M')) === 'F' ? 'F' : 'M';
        $rmsGender = $gender === 'F' ? 0 : 1;
        $hair = max(1, min(100, (int) $request->query('hair', 1)));
        $color = max(0, min(30, (int) $request->query('color', 0)));
        $dir = max(0, min(7, (int) $request->query('dir', 0)));

        $cacheDir = storage_path('app/ro-sprites');
        if (!File::isDirectory($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }

        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . "sprite_{$rmsGender}_{$job}_{$hair}_{$color}_{$dir}.png";
        if (File::exists($cacheFile) && (time() - File::lastModified($cacheFile)) < 86400 * 30) {
            return response(File::get($cacheFile), 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=2592000',
            ]);
        }

        $settings = $this->creationService->getSettings();
        $template = $settings['chargen_url'] ?: 'https://ro-character-simulator.ratemyserver.net/charsim.php?gender={rms_gender}&job={job}&hair={hair}&viewid=0&location=0&direction={dir}&action=0&hdye={color}&dye=0&framenum=0&bg=0';
        $url = str_replace(
            ['{rms_gender}', '{gender}', '{job}', '{hair}', '{color}', '{dir}'],
            [(string) $rmsGender, $gender, (string) $job, (string) $hair, (string) $color, (string) $dir],
            $template
        );

        try {
            $res = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => 'https://ro-character-simulator.ratemyserver.net/',
                ])
                ->get($url);

            if ($res->successful() && str_contains((string) $res->header('Content-Type'), 'image')) {
                $body = $res->body();
                File::put($cacheFile, $body);

                return response($body, 200, [
                    'Content-Type' => $res->header('Content-Type') ?: 'image/png',
                    'Cache-Control' => 'public, max-age=2592000',
                ]);
            }
        } catch (\Throwable $e) {
            // Fall through to 404 so client uses built-in preview fallback cleanly
        }

        return response('', 404);
    }

    /**
     * Cached proxy for static RO hairstyle head icons (Male/Female).
     */
    public function hairPreview(Request $request)
    {
        $gender = strtoupper((string) $request->query('gender', 'M')) === 'F' ? 'F' : 'M';
        $hair = max(1, min(100, (int) $request->query('hair', 1)));

        $cacheDir = storage_path('app/ro-sprites');
        if (!File::isDirectory($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }

        $cacheFile = $cacheDir . DIRECTORY_SEPARATOR . "head_{$gender}_{$hair}.gif";
        if (File::exists($cacheFile) && (time() - File::lastModified($cacheFile)) < 86400 * 30) {
            return response(File::get($cacheFile), 200, [
                'Content-Type' => 'image/gif',
                'Cache-Control' => 'public, max-age=2592000',
            ]);
        }

        $suffix = $gender === 'F' ? '%BF%A9' : '%B3%B2';
        $url = "https://ro-character-simulator.ratemyserver.net/images/heads/{$hair}_{$suffix}.gif";

        try {
            $res = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => 'https://ro-character-simulator.ratemyserver.net/',
                ])
                ->get($url);

            if ($res->successful() && str_contains((string) $res->header('Content-Type'), 'image')) {
                $body = $res->body();
                File::put($cacheFile, $body);

                return response($body, 200, [
                    'Content-Type' => $res->header('Content-Type') ?: 'image/gif',
                    'Cache-Control' => 'public, max-age=2592000',
                ]);
            }
        } catch (\Throwable $e) {
            // Fall through to 404 so client uses fallback
        }

        return response('', 404);
    }
}
