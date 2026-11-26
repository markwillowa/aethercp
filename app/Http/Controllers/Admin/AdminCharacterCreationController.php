<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\Character\CharacterCreationService;
use Illuminate\Http\Request;

class AdminCharacterCreationController extends Controller
{
    public function __construct(
        protected CharacterCreationService $creationService
    ) {}

    public function index()
    {
        $settings = $this->creationService->getSettings();
        $jobCatalog = $this->creationService->getJobCatalog();
        $hairColors = $this->creationService->getHairColorCatalog();
        $presets = $this->creationService->getPresets();

        return view('admin.character-creation.index', compact(
            'settings',
            'jobCatalog',
            'hairColors',
            'presets'
        ));
    }

    public function update(Request $request)
    {
        $serverMaxBase = max(1, (int) config('aether.rates.max_base_level', 260));
        $serverMaxJob = max(1, (int) config('aether.rates.max_job_level', 70));

        $validated = $request->validate([
            'start_map' => 'required|string|max:32',
            'start_x' => 'required|integer|min:1|max:512',
            'start_y' => 'required|integer|min:1|max:512',
            'start_zeny' => 'required|integer|min:0|max:2000000000',
            'hairstyles' => 'nullable|array',
            'hairstyles.*' => 'integer|min:1|max:100',
            'hair_colors' => 'nullable|array',
            'hair_colors.*' => 'integer|min:0|max:30',
            'default_hairstyle' => 'required|integer|min:1|max:100',
            'default_hair_color' => 'required|integer|min:0|max:30',
            'enabled_categories' => 'nullable|array',
            'enabled_categories.*' => 'string|max:32',
            'enabled_jobs' => 'nullable|array',
            'enabled_jobs.*' => 'integer|min:0|max:99999',
            'starting_base_level' => "required|integer|min:1|max:{$serverMaxBase}",
            'starting_job_level' => "required|integer|min:1|max:{$serverMaxJob}",
            'max_base_level' => "required|integer|min:1|max:{$serverMaxBase}",
            'max_job_level' => "required|integer|min:1|max:{$serverMaxJob}",
            'max_slots' => 'required|integer|min:1|max:15',
            'min_name_length' => 'required|integer|min:2|max:12',
            'max_name_length' => 'required|integer|min:6|max:23',
            'forbidden_names' => 'nullable|string|max:1000',
            'chargen_url' => 'nullable|string|max:500',
            'custom_jobs_raw' => 'nullable|string|max:4000',
            'starting_items_global_raw' => 'nullable|string|max:4000',
            'starting_items_jobs' => 'nullable|array',
        ]);

        $maxBase = max(1, (int) $validated['max_base_level']);
        $maxJob = max(1, (int) $validated['max_job_level']);
        $startBase = min($maxBase, max(1, (int) $validated['starting_base_level']));
        $startJob = min($maxJob, max(1, (int) $validated['starting_job_level']));

        $hairstyles = !empty($validated['hairstyles'])
            ? array_values(array_unique(array_map('intval', $validated['hairstyles'])))
            : [1];
        $hairColors = isset($validated['hair_colors']) && count($validated['hair_colors']) > 0
            ? array_values(array_unique(array_map('intval', $validated['hair_colors'])))
            : [0];

        $defaultHairstyle = in_array((int) $validated['default_hairstyle'], $hairstyles, true)
            ? (int) $validated['default_hairstyle']
            : $hairstyles[0];

        $defaultHairColor = in_array((int) $validated['default_hair_color'], $hairColors, true)
            ? (int) $validated['default_hair_color']
            : $hairColors[0];

        // Parse custom jobs (lines of "job_id:Job Name")
        $customJobs = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) ($validated['custom_jobs_raw'] ?? '')) as $line) {
            $line = trim($line);
            if ($line === '' || !str_contains($line, ':')) {
                continue;
            }
            [$idPart, $namePart] = explode(':', $line, 2);
            $cid = (int) trim($idPart);
            $cname = trim($namePart);
            if ($cid >= 0 && $cname !== '') {
                $customJobs[] = ['id' => $cid, 'name' => $cname];
            }
        }

        // Parse starting items (lines of "item_id:amount")
        $parseItemLines = static function (?string $raw): array {
            $items = [];
            foreach (preg_split('/\r\n|\r|\n/', (string) $raw) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $parts = explode(':', $line);
                $itemId = (int) trim($parts[0] ?? '0');
                $amount = max(1, (int) trim($parts[1] ?? '1'));
                if ($itemId > 0) {
                    $items[] = ['item_id' => $itemId, 'amount' => $amount];
                }
            }
            return $items;
        };

        $jobItems = [];
        foreach ($validated['starting_items_jobs'] ?? [] as $jobId => $rawLines) {
            $parsed = $parseItemLines((string) $rawLines);
            if (!empty($parsed)) {
                $jobItems[(string) (int) $jobId] = $parsed;
            }
        }

        $this->creationService->saveSettings([
            'enabled' => $request->has('enabled') ? '1' : '0',
            'chargen_url' => trim((string) ($validated['chargen_url'] ?? '')),
            'start_map' => trim($validated['start_map']),
            'start_x' => (int) $validated['start_x'],
            'start_y' => (int) $validated['start_y'],
            'start_zeny' => (int) $validated['start_zeny'],
            'enable_gender' => $request->has('enable_gender') ? '1' : '0',
            'hairstyles' => $hairstyles,
            'hair_colors' => $hairColors,
            'default_hairstyle' => $defaultHairstyle,
            'default_hair_color' => $defaultHairColor,
            'enabled_categories' => $validated['enabled_categories'] ?? ['novice'],
            'enabled_jobs' => array_values(array_unique(array_map('intval', $validated['enabled_jobs'] ?? [0]))),
            'custom_jobs' => $customJobs,
            'starting_base_level' => $startBase,
            'starting_job_level' => $startJob,
            'max_base_level' => $maxBase,
            'max_job_level' => $maxJob,
            'allow_custom_levels' => $request->has('allow_custom_levels') ? '1' : '0',
            'max_slots' => (int) $validated['max_slots'],
            'min_name_length' => (int) $validated['min_name_length'],
            'max_name_length' => max((int) $validated['min_name_length'], (int) $validated['max_name_length']),
            'allow_spaces_in_name' => $request->has('allow_spaces_in_name') ? '1' : '0',
            'forbidden_names' => trim((string) ($validated['forbidden_names'] ?? '')),
            'require_offline' => $request->has('require_offline') ? '1' : '0',
            'starting_items' => [
                'global' => $parseItemLines($validated['starting_items_global_raw'] ?? ''),
                'jobs' => $jobItems,
            ],
        ]);

        ActivityLog::log('admin.character_creation_update', 'Updated Online Character Creation settings.');

        return back()->with('success', 'Character Creation settings saved successfully.');
    }

    public function applyPreset(Request $request)
    {
        $validated = $request->validate([
            'preset' => 'required|string|max:40',
        ]);

        $presets = $this->creationService->getPresets();
        $key = $validated['preset'];

        if (!isset($presets[$key])) {
            return back()->with('error', 'Selected preset does not exist.');
        }

        $presetSettings = $presets[$key]['settings'] ?? [];
        $savePayload = ['preset' => $key];

        foreach ($presetSettings as $k => $v) {
            if (is_bool($v)) {
                $savePayload[$k] = $v ? '1' : '0';
            } else {
                $savePayload[$k] = $v;
            }
        }

        $this->creationService->saveSettings($savePayload);
        ActivityLog::log('admin.character_creation_preset', "Applied Character Creation preset [{$presets[$key]['label']}].");

        return back()->with('success', "Applied preset: {$presets[$key]['label']}.");
    }
}
