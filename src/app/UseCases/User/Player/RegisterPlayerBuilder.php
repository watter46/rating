<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Illuminate\Support\Collection;

use App\Models\Player;
use App\Models\PlayerInfo;
use App\UseCases\Api\SofaScore\PlayerFetcher;
use App\UseCases\Util\Season;
use Exception;

final readonly class RegisterPlayerBuilder
{
    public function __construct(private PlayerFetcher $playerFetcher)
    {
        //
    }
    
    /**
     * build
     *
     * @property Collection<int> $invalidPlayers
     * @return array
     */
    public function build(Collection $invalidPlayers)
    {
        try {
            $invalidPlayerInfoIds = $this->findInvalidPlayerInfoIds($invalidPlayers);

            $result = $this->fetchInvalidPlayers($invalidPlayers)
                ->map(function ($player) use ($invalidPlayerInfoIds) {
                    $playerInfo = $invalidPlayerInfoIds->keyBy('foot_player_id')->get($player['foot_player_id']);

                    if (!$playerInfo->id) {
                        return $player;
                    }
                    
                    $player['id'] = $playerInfo->id;

                    return $player;
                })
                ->toArray();

            return $result;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function findInvalidPlayerInfoIds(Collection $invalidPlayers): Collection
    {
        $invalidPlayerIds = $invalidPlayers->map(fn($player) => $player['id'])->toArray();

        return PlayerInfo::query()
            ->select(['id','foot_player_id'])
            ->whereIn('foot_player_id', $invalidPlayerIds)
            ->currentSeason()
            ->get();
    }

    public function fetchInvalidPlayers(Collection $invalidPlayers): Collection
    {
        return $invalidPlayers
            ->map(function (array $player) {
                $data = $this->playerFetcher->fetchOrGetFile($player);

                return ValidatePlayerData::validate($data)->createData($player);
            });
    }
}