<?php

namespace App\Helpers;

use Carbon\CarbonInterval;

class TimeConverter
{
    public static function formatSeconds(float $totalSeconds) : string {
        $totalSeconds = round($totalSeconds);

        $interval = CarbonInterval::seconds($totalSeconds);
        $interval->cascade();

        if ($interval->d > 0) {
            return sprintf("%d:%02d:%02d:%02d", $interval->d, $interval->h, $interval->i, $interval->s);
        } elseif ($interval->h > 0) {
            return sprintf("%d:%02d:%02d", $interval->h, $interval->i, $interval->s);
        } elseif ($interval->i > 0) {
            return sprintf("%d:%02d", $interval->i, $interval->s);
        } else {
            return sprintf("%d", $interval->s);
        }
    }

    public static function formatForHumans(float $totalSeconds, int $parts = -1, bool $short = false) : string {
        $totalSeconds = round($totalSeconds);

        if ($totalSeconds <= 0) return '0 seconds';

        $interval = CarbonInterval::seconds($totalSeconds);
        $interval->cascade();

        return $interval->forHumans([
            'parts' => $parts,
            'short' => $short,
            'join' => true,
            'zero' => false,
        ]);
    }
}