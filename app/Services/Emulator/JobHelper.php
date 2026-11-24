<?php

namespace App\Services\Emulator;

class JobHelper
{
    public static function getName(int $jobId): string
    {
        $jobs = config('jobs', []);
        return $jobs[$jobId] ?? "Job #{$jobId}";
    }

    public static function getBadgeColor(int $jobId): string
    {
        // 4th jobs (4200+)
        if ($jobId >= 4211 && $jobId <= 4250) {
            return 'bg-purple-950 text-purple-300 border-purple-500/40';
        }

        // 3rd jobs (4054-4066)
        if ($jobId >= 4054 && $jobId <= 4079) {
            return 'bg-cyan-950 text-cyan-300 border-cyan-500/40';
        }

        // Transcendent (4008-4022)
        if ($jobId >= 4008 && $jobId <= 4022) {
            return 'bg-blue-950 text-blue-300 border-blue-500/40';
        }

        // 2nd jobs (7-20)
        if ($jobId >= 7 && $jobId <= 20) {
            return 'bg-emerald-950 text-emerald-300 border-emerald-500/40';
        }

        // 1st jobs & novice
        return 'bg-slate-800 text-slate-300 border-slate-700';
    }

    public static function getSpriteUrl(int $jobId, string $sex = 'M'): string
    {
        $gender = strtoupper($sex) === 'F' ? 'f' : 'm';
        return "https://static.divine-pride.net/images/jobs/{$gender}/{$jobId}.png";
    }

    public static function getEquipSlotName(int $equipMask): string
    {
        return match (true) {
            ($equipMask & 256) !== 0 => 'Upper Headgear',
            ($equipMask & 512) !== 0 => 'Middle Headgear',
            ($equipMask & 1) !== 0 => 'Lower Headgear',
            ($equipMask & 16) !== 0 => 'Armor',
            ($equipMask & 2) !== 0 => 'Weapon',
            ($equipMask & 32) !== 0 => 'Shield',
            ($equipMask & 4) !== 0 => 'Garment',
            ($equipMask & 64) !== 0 => 'Shoes',
            ($equipMask & 8) !== 0 => 'Accessory Right',
            ($equipMask & 128) !== 0 => 'Accessory Left',
            ($equipMask & 1024) !== 0 => 'Costume Top',
            ($equipMask & 2048) !== 0 => 'Costume Mid',
            ($equipMask & 4096) !== 0 => 'Costume Low',
            ($equipMask & 8192) !== 0 => 'Costume Garment',
            ($equipMask & 65536) !== 0 => 'Shadow Armor',
            ($equipMask & 131072) !== 0 => 'Shadow Weapon',
            ($equipMask & 262144) !== 0 => 'Shadow Shield',
            ($equipMask & 524288) !== 0 => 'Shadow Shoes',
            ($equipMask & 1048576) !== 0 => 'Shadow Earring',
            ($equipMask & 2097152) !== 0 => 'Shadow Pendant',
            default => 'Equipment',
        };
    }
}

