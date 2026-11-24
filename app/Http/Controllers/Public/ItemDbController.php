<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\ItemRepositoryInterface;
use Illuminate\Http\Request;

class ItemDbController extends Controller
{
    public function __construct(
        protected ItemRepositoryInterface $items
    ) {}

    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));
        $type = $request->query('type');
        $page = max(1, (int) $request->query('page', 1));

        $results = null;
        if (!empty($keyword) || !empty($type)) {
            $results = $this->items->search($keyword, $type, 20, $page);
        }

        $itemTypes = $this->items->getItemTypes();

        return view('itemdb.index', compact('results', 'keyword', 'type', 'itemTypes'));
    }

    public function show(int $id)
    {
        $item = $this->items->findById($id);
        if (!$item) {
            abort(404, 'Item not found in emulator database.');
        }

        return view('itemdb.show', compact('item'));
    }

    /**
     * Monster Database Listing and Search
     */
    public function monsters(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));
        $category = $request->query('category', 'all');

        $allMonsters = $this->getMonstersDataset();

        $filtered = collect($allMonsters);

        if (!empty($keyword)) {
            $filtered = $filtered->filter(function ($m) use ($keyword) {
                return str_contains(strtolower($m['name']), strtolower($keyword))
                    || str_contains((string) $m['id'], $keyword)
                    || str_contains(strtolower($m['race']), strtolower($keyword))
                    || str_contains(strtolower($m['element']), strtolower($keyword));
            });
        }

        if ($category && $category !== 'all') {
            $filtered = $filtered->filter(function ($m) use ($category) {
                if ($category === 'mvp') {
                    return !empty($m['is_mvp']);
                }
                if ($category === 'mini') {
                    return !empty($m['is_mini']);
                }
                return strtolower($m['race']) === strtolower($category);
            });
        }

        $monsters = $filtered->values();

        return view('itemdb.monsters', compact('monsters', 'keyword', 'category'));
    }

    /**
     * Monster Single Detail View
     */
    public function showMonster(int $id)
    {
        $allMonsters = $this->getMonstersDataset();
        $monster = collect($allMonsters)->firstWhere('id', $id);

        if (!$monster) {
            // Generate standard schema for numeric ID
            $monster = [
                'id' => $id,
                'name' => 'Monster #' . $id,
                'level' => 80,
                'hp' => 50000,
                'base_exp' => 15000,
                'job_exp' => 9500,
                'size' => 'Medium',
                'race' => 'Demi-Human',
                'element' => 'Neutral 1',
                'is_mvp' => false,
                'drops' => [
                    ['name' => 'Jellopy', 'rate' => '50%'],
                    ['name' => 'Monster Card', 'rate' => '0.05%'],
                ],
                'maps' => ['prt_fild08', 'gef_fild04'],
            ];
        }

        return view('itemdb.monster-show', compact('monster'));
    }

    /**
     * Maps & Dungeons Directory
     */
    public function maps(Request $request)
    {
        $type = $request->query('type', 'all');
        $keyword = trim((string) $request->query('q', ''));

        $maps = [
            // Towns
            ['code' => 'prontera', 'name' => 'Prontera City', 'type' => 'Town', 'desc' => 'The capital city of the Rune-Midgard Kingdom.'],
            ['code' => 'geffen', 'name' => 'Geffen', 'type' => 'Town', 'desc' => 'The ancient city of magic and sorcery.'],
            ['code' => 'morocc', 'name' => 'Morroc Desert', 'type' => 'Town', 'desc' => 'An oasis city surrounded by the Sograt Desert.'],
            ['code' => 'payon', 'name' => 'Payon Town', 'type' => 'Town', 'desc' => 'Traditional eastern village nestled deep within forests.'],
            ['code' => 'alberta', 'name' => 'Alberta Port', 'type' => 'Town', 'desc' => 'The bustling maritime trading port.'],
            ['code' => 'aldebaran', 'name' => 'Al De Baran', 'type' => 'Town', 'desc' => 'Canal city famous for the monumental Clock Tower.'],
            ['code' => 'yuno', 'name' => 'Yuno', 'type' => 'Town', 'desc' => 'Floating city of scholars and the Sage Academy.'],
            ['code' => 'lighthalzen', 'name' => 'Lighthalzen', 'type' => 'Town', 'desc' => 'Prosperous metropolis dominated by Rekenber Corporation.'],

            // Dungeons
            ['code' => 'glast_01', 'name' => 'Glast Heim Castle', 'type' => 'Dungeon', 'desc' => 'The fallen citadel harboring ancient undead and dark knights.'],
            ['code' => 'c_tower1', 'name' => 'Clock Tower', 'type' => 'Dungeon', 'desc' => 'Mechanical dungeon populated by living clocks, cogs, and alarms.'],
            ['code' => 'mag_dun01', 'name' => 'Magma Dungeon', 'type' => 'Dungeon', 'desc' => 'Subterranean lava caves teeming with fire elementals.'],
            ['code' => 'abyss_01', 'name' => 'Abyss Lake', 'type' => 'Dungeon', 'desc' => 'Submerged cavern sanctuary of dragons and hydras.'],
            ['code' => 'tha_t01', 'name' => 'Thanatos Tower', 'type' => 'Dungeon', 'desc' => 'Colossal monolith sealing the fragments of Thanatos.'],
            ['code' => 'thor_v01', 'name' => 'Thor Volcano', 'type' => 'Dungeon', 'desc' => 'Hostile volcanic domain of Salamanders and Ifrit.'],
            ['code' => 'lhz_dun01', 'name' => 'Somatology Laboratory', 'type' => 'Dungeon', 'desc' => 'Clandestine bio-research facility of advanced human experiments.'],
            ['code' => 'odin_tem01', 'name' => 'Odin Temple', 'type' => 'Dungeon', 'desc' => 'Isolated island ruins dedicated to the supreme Norse deity.'],
        ];

        $filtered = collect($maps);
        if ($type !== 'all') {
            $filtered = $filtered->where('type', ucfirst($type));
        }
        if (!empty($keyword)) {
            $filtered = $filtered->filter(fn($m) => str_contains(strtolower($m['name']), strtolower($keyword)) || str_contains(strtolower($m['code']), strtolower($keyword)));
        }

        $mapList = $filtered->values();

        return view('itemdb.maps', compact('mapList', 'type', 'keyword'));
    }

    /**
     * Curated Monster Dataset with RO stats & sprites
     */
    protected function getMonstersDataset(): array
    {
        return [
            [
                'id' => 1002,
                'name' => 'Poring',
                'level' => 1,
                'hp' => 60,
                'base_exp' => 2,
                'job_exp' => 1,
                'size' => 'Medium',
                'race' => 'Plant',
                'element' => 'Water 1',
                'is_mvp' => false,
                'is_mini' => false,
                'gif' => 'poring.gif',
                'drops' => [
                    ['name' => 'Jellopy', 'rate' => '70%'],
                    ['name' => 'Apple', 'rate' => '10%'],
                    ['name' => 'Empty Bottle', 'rate' => '15%'],
                    ['name' => 'Poring Card', 'rate' => '0.05%'],
                ],
                'maps' => ['prt_fild08', 'prt_fild01', 'pay_fild04'],
            ],
            [
                'id' => 1096,
                'name' => 'Angeling',
                'level' => 45,
                'hp' => 110000,
                'base_exp' => 28000,
                'job_exp' => 18000,
                'size' => 'Medium',
                'race' => 'Angel',
                'element' => 'Holy 4',
                'is_mvp' => false,
                'is_mini' => true,
                'gif' => 'angeling.gif',
                'drops' => [
                    ['name' => 'Angeling Card', 'rate' => '0.05%'],
                    ['name' => 'Holy Water', 'rate' => '60%'],
                    ['name' => 'Yggdrasil Berry', 'rate' => '15%'],
                    ['name' => 'Angel Wing', 'rate' => '1.5%'],
                ],
                'maps' => ['prt_fild04', 'pay_fild04'],
            ],
            [
                'id' => 1588,
                'name' => 'Deviling',
                'level' => 48,
                'hp' => 125000,
                'base_exp' => 32000,
                'job_exp' => 21000,
                'size' => 'Medium',
                'race' => 'Demon',
                'element' => 'Shadow 4',
                'is_mvp' => false,
                'is_mini' => true,
                'gif' => 'deviling.gif',
                'drops' => [
                    ['name' => 'Deviling Card', 'rate' => '0.05%'],
                    ['name' => 'Evil Wing', 'rate' => '1.5%'],
                    ['name' => 'Yggdrasil Berry', 'rate' => '20%'],
                    ['name' => 'Old Blue Box', 'rate' => '10%'],
                ],
                'maps' => ['pay_fild04', 'prt_fild02'],
            ],
            [
                'id' => 1039,
                'name' => 'Baphomet',
                'level' => 81,
                'hp' => 668000,
                'base_exp' => 150000,
                'job_exp' => 98000,
                'size' => 'Large',
                'race' => 'Demon',
                'element' => 'Shadow 3',
                'is_mvp' => true,
                'is_mini' => false,
                'gif' => 'baphomet.gif',
                'drops' => [
                    ['name' => 'Baphomet Card', 'rate' => '0.05%'],
                    ['name' => 'Crescent Scythe', 'rate' => '5%'],
                    ['name' => 'Magestic Goat', 'rate' => '3%'],
                    ['name' => 'Old Card Album', 'rate' => '10%'],
                ],
                'maps' => ['prt_maze03', 'gld_dun03'],
            ],
            [
                'id' => 1751,
                'name' => 'Valkyrie Randgris',
                'level' => 99,
                'hp' => 3550000,
                'base_exp' => 420000,
                'job_exp' => 280000,
                'size' => 'Large',
                'race' => 'Angel',
                'element' => 'Holy 4',
                'is_mvp' => true,
                'is_mini' => false,
                'gif' => 'valkyrie.gif',
                'drops' => [
                    ['name' => 'Valkyrie Randgris Card', 'rate' => '0.05%'],
                    ['name' => 'Valkyrie Shield', 'rate' => '5%'],
                    ['name' => 'Valkyrian Armor', 'rate' => '5%'],
                    ['name' => 'Gungnir', 'rate' => '3%'],
                ],
                'maps' => ['odin_tem03'],
            ],
            [
                'id' => 1832,
                'name' => 'Ifrit',
                'level' => 99,
                'hp' => 3780000,
                'base_exp' => 450000,
                'job_exp' => 310000,
                'size' => 'Large',
                'race' => 'Formless',
                'element' => 'Fire 4',
                'is_mvp' => true,
                'is_mini' => false,
                'gif' => 'ifrit.gif',
                'drops' => [
                    ['name' => 'Ifrit Card', 'rate' => '0.05%'],
                    ['name' => 'Ring of Flame Lord', 'rate' => '2%'],
                    ['name' => 'Ring of Resonance', 'rate' => '2%'],
                    ['name' => 'Fireblend', 'rate' => '5%'],
                ],
                'maps' => ['thor_v03'],
            ],
            [
                'id' => 1086,
                'name' => 'Golden Thief Bug',
                'level' => 65,
                'hp' => 128000,
                'base_exp' => 45000,
                'job_exp' => 28000,
                'size' => 'Medium',
                'race' => 'Insect',
                'element' => 'Fire 2',
                'is_mvp' => true,
                'is_mini' => false,
                'gif' => 'gtb.gif',
                'drops' => [
                    ['name' => 'Golden Thief Bug Card', 'rate' => '0.05%'],
                    ['name' => 'Golden Gear', 'rate' => '5%'],
                    ['name' => 'Oridecon', 'rate' => '40%'],
                ],
                'maps' => ['prt_sewb4'],
            ],
            [
                'id' => 1150,
                'name' => 'Moonlight Flower',
                'level' => 67,
                'hp' => 190000,
                'base_exp' => 58000,
                'job_exp' => 35000,
                'size' => 'Medium',
                'race' => 'Demon',
                'element' => 'Fire 3',
                'is_mvp' => true,
                'is_mini' => false,
                'gif' => 'moonlight.gif',
                'drops' => [
                    ['name' => 'Moonlight Flower Card', 'rate' => '0.05%'],
                    ['name' => 'Moonlight Dagger', 'rate' => '5%'],
                    ['name' => 'Puppy Hat', 'rate' => '3%'],
                ],
                'maps' => ['pay_dun04'],
            ],
        ];
    }
}
