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
        return DB::connection('ragnarok')
            ->table($this->getTable())
            ->where('id', $itemId)
            ->first();
    }

    public function search(string $keyword, ?string $type = null, int $limit = 25, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $query = DB::connection('ragnarok')
            ->table($this->getTable());

        if (is_numeric($keyword)) {
            $query->where('id', (int) $keyword);
        } else {
            $query->where(function ($q) use ($keyword) {
                $q->where('name_english', 'like', "%{$keyword}%")
                  ->orWhere('name_japanese', 'like', "%{$keyword}%");
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
    }

    public function getItemTypes(): array
    {
        return [
            'Healing', 'Usable', 'Armor', 'Weapon', 'Card', 'PetEgg', 'PetArmor', 'Ammo', 'DelayConsume', 'ShadowArmor'
        ];
    }
}

