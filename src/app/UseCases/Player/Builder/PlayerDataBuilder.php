<?php declare(strict_types=1);

namespace App\UseCases\Player\Builder;

use App\UseCases\Util\Season;
use Illuminate\Support\Str;


final readonly class PlayerDataBuilder
{    
    public function __construct(private Season $season)
    {
        
    }
    /**
     * build
     *
     * @property mixed $SOFA_fetched
     * @property mixed $FOOT_fetched
     * @property array $playerList
     * @return array
     */
    public function build(
        $SOFA_fetched,
        $FOOT_fetched,
        array $playerList)
    {
        // ApiFootball
        $FOOT_players = collect($FOOT_fetched[0]->players)
            ->map(function ($player) {
                return [
                    'foot_player_id' => $player->id,
                    'name' => $player->name,
                    'number' => $player->number,
                    'season'   => $this->season->current()
                ];
            });

        // SofaScore
        $SOFA_players = collect($SOFA_fetched->data->players)
            ->map(function ($players) {
                return [
                    'sofa_player_id' => $players->player->id,
                    'name' => $players->player->name,
                    'number' => isset($players->player->shirtNumber)
                        ? $players->player->shirtNumber
                        : null
                ];
            });

        $mergedApiData = $FOOT_players
            ->map(function (array $foot_player) use ($SOFA_players) {
                $sofa_player = $SOFA_players
                    ->first(function ($sofa_player) use ($foot_player) {
                        return $this->isPlayerMatch($sofa_player, $foot_player);
                    });
                    
                if (!$sofa_player) {
                    return array_merge($foot_player, ['sofa_player_id' => null]);
                }

                return array_merge($foot_player, $sofa_player);
            });
        
        if (!$playerList) {
            return $mergedApiData->toArray();
        }

        $result = collect($playerList)
            ->map(function ($player) use ($mergedApiData) {
                $data = $mergedApiData
                    ->first(function ($playerData) use ($player) {
                        return $this->isPlayerMatch($playerData, $player);
                    });

                return array_merge($player, $data);
            })
            ->toArray();
        
        return $result;
    }

    private function isPlayerMatch($sofa_player, $foot_player)
    {
        if ($this->isNumberMatch($sofa_player, $foot_player)) {
            return true;
        }

        if ($this->isNameMatch($sofa_player, $foot_player)) {
            return true;
        }

        return false;
    }

    private function isNumberMatch($sofa_player, $foot_player): bool
    {
        if (!$sofa_player['number']) {
            return false;
        }

        return $sofa_player['number'] === $foot_player['number'];
    }

    private function isNameMatch($sofa_player, $foot_player): bool
    {
        $sofa_name = Str::after($sofa_player['name'], ' ');
        $foot_name = Str::after($foot_player['name'], ' ');

        return $sofa_name === $foot_name;
    }
}