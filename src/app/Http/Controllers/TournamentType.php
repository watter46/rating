<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Collection;

enum TournamentType: string
{
    case ALL = '';
    case PREMIER_LEAGUE = 'premier_league';
    case FA_CUP = 'fa_cup';
    case LEAGUE_CUP = 'league_cup';

    private const ERROR_MESSAGE = ': Tournament is invalid';
    private const PREMIER_LEAGUE_ID = 39;
    private const FA_CUP_ID = 45;
    private const LEAGUE_CUP_ID = 48;
        
    /**
     * Tournamentをバリデーションする
     *
     * @param  string $tournament
     * @return self
     */
    public static function fromOrFail(string $tournament): self
    {
        try {
            return self::tryFrom($tournament) ?? throw new Exception($tournament.self::ERROR_MESSAGE);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * シーズンのTournamentのみ取得する
     *
     * @return array<int>
     */
    private function inSeasonTournament(): array
    {
        return [
            self::PREMIER_LEAGUE_ID,
            self::FA_CUP_ID,
            self::LEAGUE_CUP_ID
        ];
    }
    
    /**
     * リーグIDに変換する
     *
     * @return array<int>
     */
    public function toIds(): array
    {
        return match($this) {
            self::ALL => $this->inSeasonTournament(),
            self::PREMIER_LEAGUE => [self::PREMIER_LEAGUE_ID],
            self::FA_CUP => [self::FA_CUP_ID],
            self::LEAGUE_CUP => [self::LEAGUE_CUP_ID]
        };
    }

    public static function toText(): Collection
    {
        return collect(self::cases())
            ->map(function (TournamentType $type) {
                $text = match($type) {
                    self::ALL => '-',
                    self::PREMIER_LEAGUE => 'Premier League',
                    self::FA_CUP => 'FA Cup',
                    self::LEAGUE_CUP => 'League Cup'
                };

                return [
                    'value' => $type->value,
                    'text'  => $text
                ];
            });
    }
}