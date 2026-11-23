<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display the Ragnarok screenshot and media gallery.
     */
    public function index(Request $request)
    {
        $category = $request->query('category', 'all');

        // Resolve gallery items from SiteSettings or use curated Ragnarok Chronicles gallery
        $customGalleryJson = SiteSetting::get('chronicles_gallery_data', null);
        $galleryItems = [];

        if (!empty($customGalleryJson)) {
            $decoded = json_decode($customGalleryJson, true);
            if (is_array($decoded)) {
                $galleryItems = $decoded;
            }
        }

        // Default high-quality Ragnarok Online Chronicle Gallery items
        if (empty($galleryItems)) {
            $galleryItems = [
                [
                    'id' => 1,
                    'title' => 'Capital City of Prontera',
                    'category' => 'Towns',
                    'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/screenshot_prontera.jpg']),
                    'caption' => 'The majestic heart of Rune-Midgard bustling with merchants, knights, and adventurers gathering by the fountain.',
                    'date' => 'Chronicle I',
                ],
                [
                    'id' => 2,
                    'title' => 'War of Emperium - Castle Siege',
                    'category' => 'WoE',
                    'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/feat_woe_boss.jpg']),
                    'caption' => 'Clashing guilds vying for supremacy and imperial glory in the hallowed halls of Kriemhild Castle.',
                    'date' => 'Chronicle II',
                ],
                [
                    'id' => 3,
                    'title' => 'Grand Midgard Festival',
                    'category' => 'Events',
                    'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_festival.jpg']),
                    'caption' => 'Celebrations across Midgard featuring special guild banquet events, mini-games, and rare rewards.',
                    'date' => 'Chronicle III',
                ],
                [
                    'id' => 4,
                    'title' => 'Treasure Vaults & Renewal Update',
                    'category' => 'Events',
                    'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/news_update.jpg']),
                    'caption' => 'Ancient chests unearthed in the catacombs, laden with refined ores, enchanted gear, and forgotten relics.',
                    'date' => 'Chronicle IV',
                ],
                [
                    'id' => 5,
                    'title' => 'Episode XIII: Noghaltz & Odin Shrine',
                    'category' => 'Raids',
                    'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/header_chronicles_hero_clean.jpg']),
                    'caption' => 'Expeditions into the fog-shrouded ruins and forgotten temples of the ancient Nordic gods.',
                    'date' => 'Chronicle V',
                ],
                [
                    'id' => 6,
                    'title' => 'Rogue Haven & Road to Midgard',
                    'category' => 'Towns',
                    'image' => route('theme.asset', ['theme' => 'aether-chronicles', 'path' => 'images/bottom_strip_clean.jpg']),
                    'caption' => 'The scenic highways linking Geffen, Morroc, and Payon where caravans traverse under twilight skies.',
                    'date' => 'Chronicle VI',
                ],
            ];
        }

        // Filter by category if requested
        if ($category !== 'all') {
            $galleryItems = array_values(array_filter($galleryItems, function ($item) use ($category) {
                return strtolower($item['category'] ?? '') === strtolower($category);
            }));
        }

        $categories = ['all' => 'All Media', 'Towns' => 'Towns & Realms', 'WoE' => 'War of Emperium', 'Events' => 'Events & Festivals', 'Raids' => 'Dungeons & Raids'];

        return view('gallery.index', compact('galleryItems', 'categories', 'category'));
    }
}
