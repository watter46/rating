<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors\Api;

use Illuminate\Support\Collection;


class ApiSquad
{    
    /**
     * __construct
     *
     * @param  Collection<ApiPlayer> $apiPlayers
     * @return void
     */
    private function __construct(private Collection $apiPlayers)
    {
        //
    }
    
    public static function create(Collection $rawSquad): self
    {
        $apiPlayers = $rawSquad
            ->fromStd()
            ->toCollection()
            ->dataGet('players')
            ->map(fn (Collection $player) => ApiPlayer::create($player));
                    
        return new self($apiPlayers);
    }

    public function getById(int $playerId): ApiPlayer
    {
        return $this->apiPlayers->first(fn(ApiPlayer $apiPlayer) => $apiPlayer->equal($playerId));
    }

    public function getNotInIds(Collection $playerIds)
    {
        return $this->apiPlayers
            ->filter(function (ApiPlayer $apiPlayer) use ($playerIds) {
                return $playerIds->every(fn ($id) => !$apiPlayer->equal($id));
            });
    }
}