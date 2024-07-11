<?php declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Http\Controllers\Util\PlayerFile;
use App\Infrastructure\ApiFootball\MockApiFootballRepository;
use App\Infrastructure\FlashLiveSports\MockFlashLiveSportsRepository;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Player\UpdatePlayerInfos\PlayerDataMatcher;
use App\UseCases\Util\Season;


class PlayerInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** PlayerInfoを保存する */
        Carbon::setTestNow('2023-11-01');
            
        /** @var MockApiFootballRepository $apiFootballRepository */
        $apiFootballRepository = app(MockApiFootballRepository::class);

        /** @var MockFlashLiveSportsRepository $flashLiveSportsRepository */
        $flashLiveSportsRepository = app(MockFlashLiveSportsRepository::class);
        
        $squads = $apiFootballRepository->fetchSquads();
        $teamSquad = $flashLiveSportsRepository->fetchTeamSquad();

        $data = $squads->getPlayers()
            ->map(function ($player) {
                return new PlayerInfo([
                    'name' => $player['name'],
                    'number' => $player['number'],
                    'api_football_id' => $player['id'],
                    'season' => Season::current()
                ]);
            })
            ->map(function (PlayerInfo $playerInfo) use ($teamSquad) {
                $teamSquadPlayer = $teamSquad->getByPlayerInfo(new PlayerDataMatcher($playerInfo));
                
                $playerInfo->flash_live_sports_id = $teamSquadPlayer['id'] ?? null;
                $playerInfo->flash_live_sports_image_id = $teamSquadPlayer['imageId'] ?? null;

                return $playerInfo;
            });
                    
        PlayerInfo::upsert($data->toArray(), PlayerInfo::UPSERT_UNIQUE);

        /** PlayerInfoで保存されてない選手新たに保存する */
        $apiFootballIds = (new PlayerFile)->getAll();

        /** @var Collection<int, PlayerInfo> */
        $playerInfos = PlayerInfo::query()
            ->currentSeason()
            ->whereIn('api_football_id', $apiFootballIds->toArray())
            ->get();

        $data = $apiFootballIds
            ->map(function (int $apiFootballId) use ($playerInfos) {
                return collect([
                    'apiFootballId' => $apiFootballId,
                    'model' => $playerInfos->keyBy('api_football_id')->get($apiFootballId),
                    'player' => (new PlayerFile)->get($apiFootballId)
                ]);
            })
            ->map(function (Collection $data) {
                $newPlayer = $data->get('player')[0];

                $player = collect([
                    'name' => $newPlayer->name,
                    'season' => Season::current(),
                    'number' => $newPlayer->jerseyNumber,
                    'api_football_id' => $data['apiFootballId'],
                    'flash_live_sports_id' => null
                ]);

                return $data->get('model')
                    ? $player->merge(['id' => $data->get('model')->id])
                    : $player;
            });
                    
        PlayerInfo::upsert($data->toArray(), PlayerInfo::UPSERT_UNIQUE);
    }
}
