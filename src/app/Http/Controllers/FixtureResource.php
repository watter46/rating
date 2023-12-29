<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\Fixture;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\PlayerInfo;


final readonly class FixtureResource
{
    public function __construct(
        private PlayerImageFile $playerImage,
        private LeagueImageFile $leagueImage,
        private TeamImageFile   $teamImage)
    {
        
    }
    
    /**
     * チーム、リーグ、プレイヤーのファイルパスの画像を取得する
     *
     * @param  Fixture $fixture
     * @return Collection
     */
    public function format(Fixture $fixture): Collection
    {
        $players = $fixture->playerInfos;

        return $fixture->fixture
            ->map(function ($fixture, $key) {
                return match($key) {
                    'teams'   => $this->addTeamImage($fixture),
                    'league'  => $this->addLeagueImage($fixture),
                    'lineups' => $this->addLineupImage($fixture),
                    default   => $fixture
                };
            })
            ->map(function ($fixture, $key) use ($players) {
                if ($key !== 'lineups') {
                    return $fixture;
                }

                return $this->changePlayerId($fixture, $players);
            })
            ->merge(['fixtureId' => $fixture->id]);
    }

    private function addTeamImage(array $teams): array
    {
        return collect($teams)
            ->map(function ($team) {
                return collect($team)
                    ->put('img', $this->teamImage->getByPath($team['img']));
            })
            ->toArray();
    }

    private function addLeagueImage(array $league): array
    {
        return collect($league)
            ->put('img', $this->leagueImage->getByPath($league['img']))
            ->toArray();
    }

    private function addLineupImage(array $lineup): array
    {
        return collect($lineup)
            ->dot()
            ->transform(function($item, $key) {
                if (!Str::endsWith($key, 'img')) {
                    return $item;
                }
                
                return $this->playerImage->getByPath($item);
            })
            ->undot()
            ->toArray();
    }

    private function changePlayerId(array $lineup, Collection $players): array
    {        
        $result = collect($lineup)
            ->map(function (array $lineup, $key) use ($players) {
                $changeId = function (array $player) use ($players) {                                 
                    $model = $players->first(function (PlayerInfo $model) use ($player) {
                        return $model->foot_player_id === $player['id'];
                    });

                    return collect($player)
                        ->merge([
                            'id'     => $model->id,
                            'rating' => $model->rating
                        ])
                        ->toArray();
                };
                
                if ($key === 'startXI') {
                    return collect($lineup)
                        ->map(function (array $line) use ($changeId) {
                            return collect($line)->map(fn ($player) => $changeId($player));
                        });
                }
                
                return collect($lineup)->map(fn ($player) => $changeId($player));
            });

        return $result->toArray();
    }
}