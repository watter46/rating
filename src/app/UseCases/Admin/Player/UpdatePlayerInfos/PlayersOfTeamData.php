<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdatePlayerInfos;

use Illuminate\Support\Collection;

readonly class PlayersOfTeamData
{
    private Collection $playersOfTeamData;

    public function __construct(Collection $playersOfTeamData)
    {
        $this->playersOfTeamData = $this->parse($playersOfTeamData);
    }

    public static function build(Collection $playersOfTeamData)
    {
        return new self($playersOfTeamData);
    }

    public function getData(): Collection
    {
        return $this->playersOfTeamData;
    }

    public function findOrNull(array $player)
    {
        $playerMatcher = new PlayerMatcher;

        return $this->playersOfTeamData
            ->first(function ($playersOfTeamData) use ($player, $playerMatcher) {
                return $playerMatcher->isMatch($player, $playersOfTeamData);
            });
    }

    private function parse(Collection $playersOfTeamData)
    {
        return collect($playersOfTeamData['players'])
            ->map(function ($players) {
                return [
                    'sofa_player_id' => $players->player->id,
                    'name' => $players->player->name,
                    'number' => isset($players->player->shirtNumber)
                        ? $players->player->shirtNumber
                        : null
                ];
            });
    }
}