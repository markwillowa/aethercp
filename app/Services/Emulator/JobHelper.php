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
}

