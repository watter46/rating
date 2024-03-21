<?php declare(strict_types=1);

namespace App\Models;


enum TournamentIdType: int
{
    case PREMIER_LEAGUE_ID = 39;
    case FA_CUP_ID = 45;
    case LEAGUE_CUP_ID = 48;

    /**
     * シーズンのTournamentのみ取得する
     *
     * @return array<int>
     */
    public static function inSeasonTournaments(): array
    {
        return [
            self::PREMIER_LEAGUE_ID->value,
            self::FA_CUP_ID->value,
            self::LEAGUE_CUP_ID->value
        ];
    }
}