<?php declare(strict_types=1);

namespace App\Models;


enum TournamentIdType: int
{
    case PREMIER_LEAGUE_ID = 39;
    case FA_CUP_ID = 45;
    case LEAGUE_CUP_ID = 48;

    private const DEFAULT_START_DELAY_MINUTES = 120;
    private const PREMIER_LEAGUE_FINISH_DELAY_MINUTES = 10;
    private const FA_CUP_FINISH_DELAY_MINUTES = 10;
    private const LEAGUE_CUP_FINISH_DELAY_MINUTES = 10;

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

    public function startDelayMinutes()
    {
        return collect(self::cases())
            ->map(function (TournamentType $type) {
                $text = match($type) {
                    self::PREMIER_LEAGUE_ID => 'Premier League',
                    self::FA_CUP_ID => 'FA Cup',
                    self::LEAGUE_CUP_ID => 'League Cup'
                };

                return [
                    'value' => $type->value,
                    'text'  => $text
                ];
            });
    }
}