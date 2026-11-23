<?php

namespace App\Services\Emulator\Adapters;

class RAthenaAdapter extends AbstractEmulatorAdapter
{
    public function getName(): string
    {
        return 'rathena';
    }

    public function getStatColumns(): array
    {
        $columns = [
            'str', 'agi', 'vit', 'int', 'dex', 'luk',
            'max_hp', 'hp', 'max_sp', 'sp',
            'status_point', 'skill_point'
        ];

        if ($this->isRenewal()) {
            // Trait stats introduced in 4th job renewal rAthena
            $columns = array_merge($columns, [
                'pow', 'sta', 'wis', 'spl', 'con', 'crt',
                'max_ap', 'ap', 'trait_point'
            ]);
        }

        return $columns;
    }
}

