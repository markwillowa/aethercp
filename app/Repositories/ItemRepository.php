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
        try {
            return DB::connection('ragnarok')
                ->table($this->getTable())
                ->where('id', $itemId)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
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

