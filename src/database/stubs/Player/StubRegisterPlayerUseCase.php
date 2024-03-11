<?php declare(strict_types=1);

namespace Database\Stubs\Player;

use Illuminate\Support\Collection;

use App\Models\PlayerInfo;
use App\UseCases\Util\Season;
use App\Http\Controllers\Util\PlayerFile;


class StubRegisterPlayerUseCase
{
    public function __construct(private PlayerFile $player)
    {
        //
    }
    
    public function execute()
    {
        $footPlayerIdList = $this->player->getAll();

        /** @var Collection<int, PlayerInfo> */
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('foot_player_id', $footPlayerIdList->toArray())
            ->get();

        $data = $footPlayerIdList
            ->map(function (int $footPlayerId) use ($playerInfos) {
                return collect([
                    'footPlayerId' => $footPlayerId,
                    'model' => $playerInfos->keyBy('foot_player_id')->get($footPlayerId),
                    'player' => $this->player->get($footPlayerId)
                ]);
            })
            ->map(function (Collection $data) {
                $newPlayer = $data->get('player')[0];

                $player = collect([
                        'name' => $newPlayer->name,
                        'season' => Season::current(),
                        'number' => $newPlayer->jerseyNumber,
                        'foot_player_id' => $data->get('footPlayerId'),
                        'sofa_player_id' => $newPlayer->id
                    ]);

                return $data->get('model')
                    ? $player->merge(['id' => $data->get('model')->id])
                    : $player;
            })
            ->toArray();
            
        $unique = ['id'];
        $updateColumns = ['name', 'number', 'season', 'foot_player_id', 'sofa_player_id'];
        
        PlayerInfo::upsert($data, $unique, $updateColumns);
    }
}