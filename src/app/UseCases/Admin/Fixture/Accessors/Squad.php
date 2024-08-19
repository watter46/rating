<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;


class Squad
{
    private function __construct(private Collection $players)
    {
        //
    }
    
    public static function create(Collection $rawSquad): self
    {
        $players = $rawSquad
            ->fromStd()
            ->toCollection()
            ->dataGet('players')
            ->map(function (Collection $player) {
                return [
                    'id'     => $player['id'],
                    'name'   => $player['name'],
                    'number' => $player['number']
                ];
            });
        
        return new self($players);
    }

    public function getById(int $playerId)
    {
        return $this->players->first(fn($player) => $player['id'] === $playerId);
    }

    public function getNotInIds(Collection $playerIds)
    {
        return $this->players->whereNotIn('id', $playerIds->toArray());
    }
}