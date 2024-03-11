<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\Fixture;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;


final readonly class FixtureResource
{
    public function __construct(
        private PlayerImageFile $playerImage,
        private LeagueImageFile $leagueImage,
        private TeamImageFile   $teamImage)
    {
        
    }
    
    /**
     * データをフォーマットする
     *
     * @param  Fixture $fixture
     * @return Collection
     */
    public function format(Fixture $fixture): Collection
    {
        $players = $fixture->playerInfos;

        $playerCount = $players->count();
            
        return $fixture->fixture
            ->map(function ($fixture, $key) use ($players) {
                return match($key) {
                    'teams'   => $this->addTeamImage($fixture),
                    'league'  => $this->addLeagueImage($fixture),
                    'lineups' => $this->lineupResource($fixture, $players),
                    default   => $fixture
                };
            })
            ->merge([
                'fixtureId'   => $fixture->id,
                'playerCount' => $playerCount,
                'canRate'     => $fixture->canRate()
            ]);
    }
    
    /**
     * Teamの画像を取得する
     *
     * @param  array $teams
     * @return array
     */
    private function addTeamImage(array $teams): array
    {
        return collect($teams)
            ->map(function ($team) {
                return collect($team)
                    ->put('img', $this->teamImage->getByPath($team['img']));
            })
            ->toArray();
    }
    
    /**
     * Leagueの画像を取得する
     *
     * @param  array $league
     * @return array
     */
    private function addLeagueImage(array $league): array
    {
        return collect($league)
            ->put('img', $this->leagueImage->getByPath($league['img']))
            ->toArray();
    }

    private function toLastName(string $dotValue): string
    {
        return Str::afterLast($dotValue, ' ');
    }

    private function lineupResource(array $lineup, Collection $playerInfos)
    {
        return collect($lineup)
            ->map(fn($lineups) =>
                collect($lineups)
                    ->map(fn($players) =>
                collect($players)
                    ->map(function ($player) use ($playerInfos) {
                        $playerInfo = $playerInfos->keyBy('foot_player_id')->get($player['id']);

                        $player['id']     = $playerInfo->id;
                        $player['name']   = $this->toLastName($player['name']);
                        $player['img']    = $this->playerImage->getByPath($player['img']);
                        $player['rating'] = $playerInfo->rating;

                        return $player;
                    })
            ))
            ->toArray();
    }
}