<?php declare(strict_types=1);

namespace App\UseCases\Player\Builder;

use App\Models\PlayerInfo;
use App\UseCases\Util\Season;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;


final readonly class PlayerDataBuilder
{
    /**
     * build
     *
     * @property mixed $SOFA_fetched
     * @property mixed $FOOT_fetched
     * @property Collection<int, PlayerInfo> $playerInfoList
     * @return array
     */
    public function build(
        string $SOFA_fetched,
        string $FOOT_fetched,
        Collection $playerInfoList)
    {
        // ApiFootball
        $FOOT_players = collect(json_decode($FOOT_fetched)->response[0]->players)
            ->map(function ($player) {
                return [
                    'foot_player_id' => $player->id,
                    'name' => $player->name,
                    'number' => $player->number,
                    'season' => Season::current()
                ];
            });

        // SofaScore
        $SOFA_players = collect(json_decode($SOFA_fetched)->data->players)
            ->map(function ($players) {
                return [
                    'sofa_player_id' => $players->player->id,
                    'name' => $players->player->name,
                    'number' => isset($players->player->shirtNumber)
                        ? $players->player->shirtNumber
                        : null
                ];
            });

        return $this->mergePlayerData($playerInfoList, $FOOT_players, $SOFA_players);
    }
    
    /**
     * 各種プレイヤーデータをマージする
     *
     * @param  Collection<int, PlayerInfo> $playerInfoList
     * @param  mixed $FOOT_players
     * @param  mixed $SOFA_players
     * @return array
     */
    private function mergePlayerData(Collection $playerInfoList, $FOOT_players, $SOFA_players)
    {
        // SofaScoreとApiFootballのプレイヤーデータを統合する
        $mergedApiPlayerList = $FOOT_players
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
        
        if ($playerInfoList->isEmpty()) {
            return $mergedApiPlayerList->toArray();
        }

        // PlayerInfoに統合したAPIデータを統合する
        $result = $playerInfoList
            ->map(function (PlayerInfo $playerInfo) use ($mergedApiPlayerList) {
                $apiPlayer = $mergedApiPlayerList
                    ->first(function ($playerData) use ($playerInfo) {
                        return $this->isPlayerMatch($playerData, $playerInfo);
                    });

                if (!$apiPlayer || !$playerInfo) {
                    return $playerInfo->toArray();
                }

                return array_merge($playerInfo->toArray(), $apiPlayer);
            })
            ->toArray();

        return $result;
    }
    
    /**
     * プレイヤーが一致するか判定する
     *
     * @param  mixed $sofa_player
     * @param  mixed $foot_player
     * @return bool
     */
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
    
    /**
     * プレイヤーの背番号が一致するか判定する
     *
     * @param  mixed $sofa_player
     * @param  mixed $foot_player
     * @return bool
     */
    private function isNumberMatch($sofa_player, $foot_player): bool
    {
        if (!$sofa_player['number']) {
            return false;
        }

        return $sofa_player['number'] === $foot_player['number'];
    }
    
    /**
     * プレイヤーの名前が一致するか判定する
     *
     * @param  mixed $sofa_player
     * @param  mixed $foot_player
     * @return bool
     */
    private function isNameMatch($sofa_player, $foot_player): bool
    {
        $sofa_name = Str::after($sofa_player['name'], ' ');
        $foot_name = Str::after($foot_player['name'], ' ');

        return $sofa_name === $foot_name;
    }
}