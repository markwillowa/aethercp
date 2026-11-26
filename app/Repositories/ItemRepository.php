<?php

namespace App\Repositories;

use App\Contracts\EmulatorInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ItemRepository implements ItemRepositoryInterface
{
    public function __construct(
        protected EmulatorInterface $emulator
    ) {}

    protected function getTable(): string
    {
        return $this->emulator->getTableName('item_db');
    }

    public function findById(int $itemId): ?object
    {
        if ($itemId <= 0) {
            return null;
        }

        try {
            $row = DB::connection('ragnarok')
                ->table($this->getTable())
                ->where('id', $itemId)
                ->first();

            if (!$row && $this->getTable() !== 'item_db2') {
                $hasDb2 = \Illuminate\Support\Facades\Schema::connection('ragnarok')->hasTable('item_db2');
                if ($hasDb2) {
                    $row = DB::connection('ragnarok')
                        ->table('item_db2')
                        ->where('id', $itemId)
                        ->first();
                }
            }

            if ($row) {
                if (empty($row->name_english)) {
                    $row->name_english = $row->name_japanese ?? $row->name ?? $row->name_aegis ?? null;
                }
                return $row;
            }
        } catch (\Throwable $e) {
            // Fall through to fallback item resolver
        }

        return $this->resolveFallbackItem($itemId);
    }

    /**
     * Resolve basic item metadata when item_db is empty or missing an entry.
     */
    protected function resolveFallbackItem(int $itemId): ?object
    {
        $known = [
            501 => ['Red Potion', 0, 'Healing'],
            502 => ['Orange Potion', 0, 'Healing'],
            503 => ['Yellow Potion', 0, 'Healing'],
            504 => ['White Potion', 0, 'Healing'],
            505 => ['Blue Potion', 0, 'Healing'],
            522 => ['Mastela Fruit', 0, 'Healing'],
            601 => ['Fly Wing', 0, 'Usable'],
            602 => ['Butterfly Wing', 0, 'Usable'],
            603 => ['Old Blue Box', 0, 'Usable'],
            607 => ['Yggdrasil Berry', 0, 'Healing'],
            608 => ['Yggdrasil Seed', 0, 'Healing'],
            610 => ['Yggdrasil Leaf', 0, 'Usable'],
            616 => ['Old Card Album', 0, 'Usable'],
            617 => ['Old Purple Box', 0, 'Usable'],
            644 => ['Gift Box', 0, 'Usable'],
            715 => ['Yellow Gemstone', 0, 'Etc'],
            716 => ['Red Gemstone', 0, 'Etc'],
            717 => ['Blue Gemstone', 0, 'Etc'],
            909 => ['Jellopy', 0, 'Etc'],
            984 => ['Oridecon', 0, 'Etc'],
            985 => ['Elunium', 0, 'Etc'],
            1107 => ['Blade', 4, 'Weapon'],
            1119 => ['Tsurugi', 2, 'Weapon'],
            1123 => ['Haedonggum', 2, 'Weapon'],
            1161 => ['Balmung', 0, 'Weapon'],
            1220 => ['Ice Pick', 1, 'Weapon'],
            1704 => ['Composite Bow', 4, 'Weapon'],
            2101 => ['Guard', 1, 'Armor'],
            2115 => ["Valkyrja's Shield", 1, 'Armor'],
            2256 => ['Elven Ears', 0, 'Armor'],
            2357 => ['Valkyrian Armor', 1, 'Armor'],
            2421 => ['Valkyrian Shoes', 1, 'Armor'],
            2524 => ['Valkyrian Manteau', 1, 'Armor'],
            2620 => ['Ring of Flame Lord', 0, 'Armor'],
            4001 => ['Poring Card', 0, 'Card'],
            4002 => ['Fabre Card', 0, 'Card'],
            4003 => ['Pupa Card', 0, 'Card'],
            4004 => ['Drops Card', 0, 'Card'],
            4005 => ['Santa Poring Card', 0, 'Card'],
            4006 => ['Lunatic Card', 0, 'Card'],
            4013 => ['Andre Card', 0, 'Card'],
            4035 => ['Hydra Card', 0, 'Card'],
            4054 => ['Angeling Card', 0, 'Card'],
            4058 => ['Thara Frog Card', 0, 'Card'],
            4064 => ['Zerom Card', 0, 'Card'],
            4092 => ['Skeleton Worker Card', 0, 'Card'],
            4105 => ['Marc Card', 0, 'Card'],
            4121 => ['Phreeoni Card', 0, 'Card'],
            4128 => ['Golden Thief Bug Card', 0, 'Card'],
            4131 => ['Moonlight Flower Card', 0, 'Card'],
            4133 => ['Raydric Card', 0, 'Card'],
            4140 => ['Abysmal Knight Card', 0, 'Card'],
            4142 => ['Ghostring Card', 0, 'Card'],
            4144 => ['Osiris Card', 0, 'Card'],
            4148 => ['Pharaoh Card', 0, 'Card'],
            4236 => ['Amon Ra Card', 0, 'Card'],
            4305 => ['Turtle General Card', 0, 'Card'],
            4357 => ['Lord Knight Card', 0, 'Card'],
            4359 => ['Assassin Cross Card', 0, 'Card'],
            4361 => ['Mastersmith Card', 0, 'Card'],
            4363 => ['High Priest Card', 0, 'Card'],
            4365 => ['High Wizard Card', 0, 'Card'],
            4367 => ['Sniper Card', 0, 'Card'],
            4372 => ['White Lady Card', 0, 'Card'],
            4399 => ['Thanatos Card', 0, 'Card'],
            4403 => ['Kiel-D-01 Card', 0, 'Card'],
            4407 => ['Randgris Card', 0, 'Card'],
            4408 => ['Gloomy Night Card', 0, 'Card'],
            4419 => ['Ktullanux Card', 0, 'Card'],
            4441 => ['Fallen Bishop Card', 0, 'Card'],
            5013 => ["Lord Kaho's Horn", 0, 'Armor'],
            5123 => ["Ulle's Cap", 1, 'Armor'],
            12020 => ['Cursed Water', 0, 'Usable'],
        ];

        if (isset($known[$itemId])) {
            [$name, $slots, $type] = $known[$itemId];
            return (object) [
                'id' => $itemId,
                'name_english' => $name,
                'name_aegis' => str_replace([' ', "'"], ['_', ''], $name),
                'type' => $type,
                'slots' => $slots,
            ];
        }

        if (app()->runningUnitTests()) {
            return null;
        }

        return Cache::remember("aether_item_fallback_{$itemId}", 86400 * 30, function () use ($itemId) {
            try {
                $res = \Illuminate\Support\Facades\Http::timeout(2)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get("https://www.divine-pride.net/database/item/{$itemId}");

                if ($res->successful() && preg_match('/<title>(.*?)\s*(?:-|—|\|)\s*Divine Pride/i', $res->body(), $m)) {
                    $title = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
                    $slots = 0;
                    if (preg_match('/\[(\d+)\]$/', $title, $sm)) {
                        $slots = (int) $sm[1];
                        $title = trim(preg_replace('/\s*\[\d+\]$/', '', $title));
                    }
                    if ($title !== '') {
                        $type = str_ends_with(strtolower($title), ' card') ? 'Card' : 'Etc';
                        return (object) [
                            'id' => $itemId,
                            'name_english' => $title,
                            'name_aegis' => str_replace([' ', "'"], ['_', ''], $title),
                            'type' => $type,
                            'slots' => $slots,
                        ];
                    }
                }
            } catch (\Throwable) {
            }

            return null;
        });
    }

    protected function getSearchableNameColumns(): array
    {
        return Cache::remember('aether_item_db_name_cols_' . $this->getTable(), 3600, function () {
            try {
                $cols = DB::connection('ragnarok')
                    ->getSchemaBuilder()
                    ->getColumnListing($this->getTable());

                $candidates = ['name_english', 'name_aegis', 'name_japanese'];
                $matched = array_values(array_intersect($candidates, $cols));

                return !empty($matched) ? $matched : ['name_english'];
            } catch (\Throwable $e) {
                return ['name_english'];
            }
        });
    }

    public function search(string $keyword, ?string $type = null, int $limit = 25, int $page = 1): array
    {
        try {
            $offset = ($page - 1) * $limit;

            $query = DB::connection('ragnarok')
                ->table($this->getTable());

            if (is_numeric($keyword)) {
                $query->where('id', (int) $keyword);
            } else {
                $nameCols = $this->getSearchableNameColumns();
                $query->where(function ($q) use ($keyword, $nameCols) {
                    foreach ($nameCols as $i => $col) {
                        if ($i === 0) {
                            $q->where($col, 'like', "%{$keyword}%");
                        } else {
                            $q->orWhere($col, 'like', "%{$keyword}%");
                        }
                    }
                });
            }

            if ($type) {
                $query->where('type', $type);
            }

            $total = (clone $query)->count();
            $items = $query->offset($offset)->limit($limit)->get()->toArray();

            return [
                'total' => $total,
                'items' => $items,
                'page' => $page,
                'last_page' => max(1, (int) ceil($total / $limit)),
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Item search query error: " . $e->getMessage());
            return [
                'total' => 0,
                'items' => [],
                'page' => 1,
                'last_page' => 1,
            ];
        }
    }

    public function getItemTypes(): array
    {
        return [
            'Healing', 'Usable', 'Armor', 'Weapon', 'Card', 'PetEgg', 'PetArmor', 'Ammo', 'DelayConsume', 'ShadowArmor'
        ];
    }
}

