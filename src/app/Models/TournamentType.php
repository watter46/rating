<?php declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Support\Collection;


enum TournamentType: string
{
    case ALL = '';
    case PREMIER_LEAGUE = 'premier_league';
    case FA_CUP = 'fa_cup';
    case LEAGUE_CUP = 'league_cup';

    private const ERROR_MESSAGE = ': Tournament is invalid';
        
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
     * リーグIDに変換する
     *
     * @return array<int>
     */ 
    public function toIds(): array
    {
        return match($this) {
            self::ALL            => TournamentIdType::inSeasonTournaments(),
            self::PREMIER_LEAGUE => [TournamentIdType::PREMIER_LEAGUE_ID->value],
            self::FA_CUP         => [TournamentIdType::FA_CUP_ID->value],
            self::LEAGUE_CUP     => [TournamentIdType::LEAGUE_CUP_ID->value]
        };
    }
    
    /**
     * 表示用に変換する
     *
     * @return Collection
     */
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