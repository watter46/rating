<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Livewire\User\Data\FixtureDataPresenter;


final readonly class FixturePresenter
{
    private const LINEUPS_KEYS = ['startXI', 'substitutes'];
    
    public function format(Fixture $fixture): Collection
    {
        $newFixture = FixtureDataPresenter::create($fixture)
            ->formatFormation()
            ->formatSubstitutes()
            ->formatPlayerData($fixture->fixtureInfo->playerInfos)
            ->addPlayerCountColumn()
            ->get();

        $players = $fixture->newPlayers->keyBy('player_info_id');

        $lineupsData = $this->addPlayerGridCss($newFixture->fixtureInfo->lineups)
            ->map(function ($data, $key) use ($players) {
                if (collect(self::LINEUPS_KEYS)->some($key)) {
                    return $data->map(function (Collection $playersData) use ($players) {
                        return $playersData
                            ->map(function (array $playerData) use ($players) {
                                return [
                                    'playerData' => $playerData,
                                    'player' => $players->get($playerData['id'])
                                ];
                            });
                    });
                }

                return $data;
            });
            
        return collect([
            'fixtureId'     => $newFixture->id,
            'fixtureInfoId' => $newFixture->fixtureInfo->id,
            'momCount'      => $newFixture->mom_count,
            'momLimit'      => $newFixture->momLimit,
            'scoreData'     => $newFixture->fixtureInfo->score,
            'teamsData'     => $newFixture->fixtureInfo->teams,
            'leagueData'    => $newFixture->fixtureInfo->league,
            'fixtureData'   => $newFixture->fixtureInfo->fixture,
            'lineupsData'   => $lineupsData
        ]);
    }

    private function addPlayerGridCss(Collection $lineupsData): Collection
    {
        $count = $lineupsData['playerCount'];

        $countIsOne = $count === 1;
        
        return collect($lineupsData)
            ->put('playerGridCss', $countIsOne ? 'w-full' : 'w-1/'.$count);
    }
}