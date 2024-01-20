<?php declare(strict_types=1);

namespace App\UseCases\Util;


final readonly class Season
{
    private const SEASON_END_MONTH = 6;
    
    public static function current(): int
    {
        $now = now();

        $season = $now->year;

        if (1 <= $now->month && $now->month <= self::SEASON_END_MONTH) {
            return $season - 1;
        }

        return $season;
    }
}