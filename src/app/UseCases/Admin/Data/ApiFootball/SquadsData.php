<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Player\Processors\PlayerInfos\PlayerDataMatcher;


readonly class SquadsData
{
    private function __construct(private Collection $squadsData)
    {
        //
    }
    
    public static function create(Collection $squadsData): self
    {
        return new self($squadsData);
    }

    public function getPlayers(): Collection
    {
        return collect($this->squadsData->get('players'))
            ->map(function ($player) {
                return [
                    'id'     => $player->id,
                    'name'   => $player->name,
                    'number' => $player->number
                ];
            });
    }

    public function getByPlayerInfo(PlayerDataMatcher $matcher)
    {
        return $this->getPlayers()->first(fn ($player) => $matcher->match($player));
    }
}