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
     * @return int
     */
    public function toId(): ?int
    {
        return match($this) {
            self::ALL => null,
            self::PREMIER_LEAGUE => 39,
            self::FA_CUP => 45,
            self::LEAGUE_CUP => 48
        };
    }
    
    /**
     * すべてのツアーを取得するか判定する
     *
     * @return bool
     */
    public function isAll(): bool
    {
        return $this === self::ALL;
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