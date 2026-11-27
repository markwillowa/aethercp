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

    /**
     * All candidate item_db tables, including split equip/usable/etc SQL tables.
     */
    protected function getAvailableItemTables(): array
    {
        return Cache::remember('aether_available_item_db_tables', 60, function () {
            $candidates = array_unique(array_filter([
                $this->getTable(),
                'item_db',
                'item_db_re',
                'item_db2',
                'item_db2_re',
                'item_db_equip',
                'item_db_re_equip',
                'item_db2_equip',
                'item_db2_re_equip',
                'item_db_usable',
                'item_db_re_usable',
                'item_db2_usable',
                'item_db2_re_usable',
                'item_db_etc',
                'item_db_re_etc',
                'item_db2_etc',
                'item_db2_re_etc',
            ]));

            $available = [];
            $schema = \Illuminate\Support\Facades\Schema::connection('ragnarok');
            foreach ($candidates as $table) {
                try {
                    if ($schema->hasTable($table)) {
                        $available[] = $table;
                    }
                } catch (\Throwable) {
                }
            }

            return !empty($available) ? $available : [$this->getTable()];
        });
    }

    /**
     * Determine top-level category ('Equip', 'Usable', or 'Etc') from a split table name.
     */
    protected function getTableCategory(string $table): ?string
    {
        if (str_ends_with($table, '_equip')) {
            return 'Equip';
        }
        if (str_ends_with($table, '_usable')) {
            return 'Usable';
        }
        if (str_ends_with($table, '_etc')) {
            return 'Etc';
        }

        return null;
    }

    /**
     * Categorize an item into one of the 3 SQL categories: Equip, Usable, or Etc.
     */
    public function resolveCategory(object $item, ?string $sourceTable = null): string
    {
        if ($sourceTable) {
            $tableCat = $this->getTableCategory($sourceTable);
            if ($tableCat !== null) {
                return $tableCat;
            }
        }

        $rawType = strtolower(trim((string) ($item->type ?? '')));
        if (in_array($rawType, ['weapon', 'armor', 'shadowarmor', 'petarmor', 'equip', '4', '5', '8', '12'], true)) {
            return 'Equip';
        }
        if (in_array($rawType, ['healing', 'usable', 'delayconsume', 'cash', '0', '2', '11', '18'], true)) {
            return 'Usable';
        }

        return 'Etc';
    }

    /**
     * Map legacy numeric rAthena/Hercules item types to canonical string names.
     */
    protected function normalizeItemTypeName(mixed $rawType, string $fallbackCategory): string
    {
        $numericMap = [
            '0' => 'Healing',
            '2' => 'Usable',
            '3' => 'Etc',
            '4' => 'Weapon',
            '5' => 'Armor',
            '6' => 'Card',
            '7' => 'PetEgg',
            '8' => 'PetArmor',
            '10' => 'Ammo',
            '11' => 'DelayConsume',
            '12' => 'ShadowArmor',
            '18' => 'Cash',
        ];

        $str = trim((string) ($rawType ?? ''));
        if ($str === '') {
            return $fallbackCategory;
        }

        return $numericMap[$str] ?? $str;
    }

    protected function normalizeItemRow(object $row, ?string $sourceTable = null): object
    {
        $category = $this->resolveCategory($row, $sourceTable);
        $row->category = $category;
        $row->source_table = $sourceTable;
        $row->type = $this->normalizeItemTypeName($row->type ?? null, $category);

        if (empty($row->name_english)) {
            $row->name_english = $row->name_japanese ?? $row->name ?? $row->name_aegis ?? "Item #{$row->id}";
        }

        return $row;
    }

    public function findById(int $itemId): ?object
    {
        if ($itemId <= 0) {
            return null;
        }

        foreach ($this->getAvailableItemTables() as $table) {
            try {
                $row = DB::connection('ragnarok')
                    ->table($table)
                    ->where('id', $itemId)
                    ->first();

                if ($row) {
                    return $this->normalizeItemRow($row, $table);
                }
            } catch (\Throwable) {
            }
        }

        $fallback = $this->resolveFallbackItem($itemId);
        if ($fallback) {
            return $this->normalizeItemRow($fallback, null);
        }

        return null;
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

    protected function getSearchableNameColumns(?string $table = null): array
    {
        $table = $table ?: $this->getTable();
        return Cache::remember('aether_item_db_name_cols_' . $table, 3600, function () use ($table) {
            try {
                $cols = DB::connection('ragnarok')
                    ->getSchemaBuilder()
                    ->getColumnListing($table);

                $candidates = ['name_english', 'name_aegis', 'name_japanese', 'name'];
                $matched = array_values(array_intersect($candidates, $cols));

                return !empty($matched) ? $matched : ['name_english'];
            } catch (\Throwable $e) {
                return ['name_english'];
            }
        });
    }

    /**
     * Map a category or sub-type filter to matching DB type values (string + numeric).
     */
    protected function getTypeFilterValues(string $type): array
    {
        return match (strtolower($type)) {
            'equip' => ['Weapon', 'Armor', 'ShadowArmor', 'PetArmor', 'Equip', '4', '5', '8', '12'],
            'usable' => ['Healing', 'Usable', 'DelayConsume', 'Cash', '0', '2', '11', '18'],
            'etc' => ['Etc', 'Card', 'Ammo', 'PetEgg', 'Taming', '3', '6', '7', '10'],
            'healing' => ['Healing', '0'],
            'usable_only' => ['Usable', '2'],
            'etc_only' => ['Etc', '3'],
            'weapon' => ['Weapon', '4'],
            'armor' => ['Armor', '5'],
            'card' => ['Card', '6'],
            'petegg' => ['PetEgg', '7'],
            'petarmor' => ['PetArmor', '8'],
            'ammo' => ['Ammo', '10'],
            'delayconsume' => ['DelayConsume', '11'],
            'shadowarmor' => ['ShadowArmor', '12'],
            'cash' => ['Cash', '18'],
            default => [$type],
        };
    }

    /**
     * Determine which main SQL category a sub-type belongs to, if any.
     */
    protected function getParentCategoryForType(string $type): ?string
    {
        return match (strtolower($type)) {
            'equip', 'weapon', 'armor', 'shadowarmor', 'petarmor' => 'Equip',
            'usable', 'healing', 'delayconsume', 'cash' => 'Usable',
            'etc', 'card', 'ammo', 'petegg', 'taming' => 'Etc',
            default => null,
        };
    }

    public function search(string $keyword, ?string $type = null, int $limit = 25, int $page = 1): array
    {
        try {
            $offset = ($page - 1) * $limit;
            $type = $type ? trim($type) : null;
            $parentCat = $type ? $this->getParentCategoryForType($type) : null;
            $isTopCategory = $type && in_array(strtolower($type), ['equip', 'usable', 'etc'], true);

            $collected = [];

            foreach ($this->getAvailableItemTables() as $table) {
                $tableCat = $this->getTableCategory($table);

                // Skip split tables that don't belong to the requested category/type
                if ($tableCat !== null && $parentCat !== null && $tableCat !== $parentCat) {
                    continue;
                }

                try {
                    $query = DB::connection('ragnarok')->table($table);

                    if ($keyword !== '') {
                        if (is_numeric($keyword)) {
                            $query->where('id', (int) $keyword);
                        } else {
                            $nameCols = $this->getSearchableNameColumns($table);
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
                    }

                    // Apply type filter on unified tables (or when filtering by sub-type on a split table)
                    if ($type && !($isTopCategory && $tableCat === $parentCat)) {
                        $filterValues = $this->getTypeFilterValues($type);
                        $query->whereIn('type', $filterValues);
                    }

                    $rows = $query->get();
                    foreach ($rows as $row) {
                        $normalized = $this->normalizeItemRow($row, $table);
                        $id = (int) $normalized->id;
                        if (!isset($collected[$id])) {
                            $collected[$id] = $normalized;
                        }
                    }
                } catch (\Throwable $tableErr) {
                    \Illuminate\Support\Facades\Log::warning("Item search error on table {$table}: " . $tableErr->getMessage());
                }
            }

            ksort($collected);
            $allItems = array_values($collected);
            $total = count($allItems);
            $items = array_slice($allItems, $offset, $limit);

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
            'Equip',
            'Usable',
            'Etc',
            'Weapon',
            'Armor',
            'Healing',
            'Card',
            'Ammo',
            'DelayConsume',
            'ShadowArmor',
            'PetEgg',
            'PetArmor',
        ];
    }
}

