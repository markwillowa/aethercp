<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminGalleryController extends Controller
{
    /**
     * Get the default curated gallery items.
     */
    public static function getDefaultGalleryItems(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Capital City of Prontera',
                'category' => 'Towns',
                'image' => '/themes/aether-chronicles/assets/images/screenshot_prontera.jpg',
                'caption' => 'The majestic heart of Rune-Midgard bustling with merchants, knights, and adventurers gathering by the fountain.',
                'date' => 'Chronicle I',
            ],
            [
                'id' => 2,
                'title' => 'War of Emperium - Castle Siege',
                'category' => 'WoE',
                'image' => '/themes/aether-chronicles/assets/images/feat_woe_boss.jpg',
                'caption' => 'Clashing guilds vying for supremacy and imperial glory in the hallowed halls of Kriemhild Castle.',
                'date' => 'Chronicle II',
            ],
            [
                'id' => 3,
                'title' => 'Grand Midgard Festival',
                'category' => 'Events',
                'image' => '/themes/aether-chronicles/assets/images/news_festival.jpg',
                'caption' => 'Celebrations across Midgard featuring special guild banquet events, mini-games, and rare rewards.',
                'date' => 'Chronicle III',
            ],
            [
                'id' => 4,
                'title' => 'Treasure Vaults & Renewal Update',
                'category' => 'Events',
                'image' => '/themes/aether-chronicles/assets/images/news_update.jpg',
                'caption' => 'Ancient chests unearthed in the catacombs, laden with refined ores, enchanted gear, and forgotten relics.',
                'date' => 'Chronicle IV',
            ],
            [
                'id' => 5,
                'title' => 'Episode XIII: Noghaltz & Odin Shrine',
                'category' => 'Raids',
                'image' => '/themes/aether-chronicles/assets/images/header_chronicles_hero_clean.jpg',
                'caption' => 'Expeditions into the fog-shrouded ruins and forgotten temples of the ancient Nordic gods.',
                'date' => 'Chronicle V',
            ],
            [
                'id' => 6,
                'title' => 'Rogue Haven & Road to Midgard',
                'category' => 'Towns',
                'image' => '/themes/aether-chronicles/assets/images/bottom_strip_clean.jpg',
                'caption' => 'The scenic highways linking Geffen, Morroc, and Payon where caravans traverse under twilight skies.',
                'date' => 'Chronicle VI',
            ],
        ];
    }

    /**
     * Load current gallery items from settings.
     */
    protected function getGalleryItems(): array
    {
        $raw = SiteSetting::get('chronicles_gallery_data');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }
        return self::getDefaultGalleryItems();
    }

    /**
     * Save gallery items to settings.
     */
    protected function saveGalleryItems(array $items): void
    {
        SiteSetting::set('chronicles_gallery_data', json_encode(array_values($items)));
    }

    /**
     * Display the admin gallery manager interface.
     */
    public function index(Request $request)
    {
        $galleryItems = $this->getGalleryItems();
        $categories = ['Towns', 'WoE', 'Events', 'Raids', 'Community'];

        return view('admin.gallery.index', compact('galleryItems', 'categories'));
    }

    /**
     * Store a new screenshot / gallery item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'category' => 'required|string|max:50',
            'image' => 'required|string|max:500',
            'caption' => 'nullable|string|max:500',
            'date' => 'nullable|string|max:50',
        ]);

        $items = $this->getGalleryItems();
        $nextId = count($items) > 0 ? (max(array_column($items, 'id')) + 1) : 1;

        $newItem = [
            'id' => $nextId,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'image' => $validated['image'],
            'caption' => $validated['caption'] ?? '',
            'date' => $validated['date'] ?? 'Chronicle ' . $nextId,
        ];

        $items[] = $newItem;
        $this->saveGalleryItems($items);

        ActivityLog::record(
            'gallery_created',
            "Added gallery screenshot '{$validated['title']}' under {$validated['category']}",
            ['item' => $newItem]
        );

        return redirect()->route('admin.gallery.index')->with('success', "Gallery screenshot '{$validated['title']}' added successfully!");
    }

    /**
     * Update an existing gallery item.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'category' => 'required|string|max:50',
            'image' => 'required|string|max:500',
            'caption' => 'nullable|string|max:500',
            'date' => 'nullable|string|max:50',
        ]);

        $items = $this->getGalleryItems();
        $found = false;

        foreach ($items as &$item) {
            if ($item['id'] == $id) {
                $item['title'] = $validated['title'];
                $item['category'] = $validated['category'];
                $item['image'] = $validated['image'];
                $item['caption'] = $validated['caption'] ?? '';
                $item['date'] = $validated['date'] ?? $item['date'] ?? 'Chronicle';
                $found = true;
                break;
            }
        }

        if (!$found) {
            return redirect()->route('admin.gallery.index')->with('error', 'Gallery screenshot not found.');
        }

        $this->saveGalleryItems($items);

        ActivityLog::record(
            'gallery_updated',
            "Updated gallery screenshot #{$id} '{$validated['title']}'",
            ['id' => $id, 'data' => $validated]
        );

        return redirect()->route('admin.gallery.index')->with('success', "Gallery screenshot '{$validated['title']}' updated successfully!");
    }

    /**
     * Remove a screenshot from the gallery.
     */
    public function destroy($id)
    {
        $items = $this->getGalleryItems();
        $filtered = array_filter($items, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        $this->saveGalleryItems($filtered);

        ActivityLog::record(
            'gallery_deleted',
            "Deleted gallery screenshot #{$id}",
            ['id' => $id]
        );

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery screenshot removed successfully.');
    }

    /**
     * Reset gallery to default curated screenshots.
     */
    public function resetDefaults()
    {
        $defaults = self::getDefaultGalleryItems();
        $this->saveGalleryItems($defaults);

        ActivityLog::record(
            'gallery_reset',
            'Reset gallery to default curated Ragnarok Chronicles screenshots'
        );

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery reset to default curated media successfully.');
    }
}
