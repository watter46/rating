<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Livewire\User\Data\FixtureDataPresenter;


final readonly class FixturePresenter
{
    public function format(Fixture $fixture): Collection
    {
        $fixture->fixture = FixtureDataPresenter::create($fixture)
            ->formatFormation()
            ->formatSubstitutes()
            ->formatPathToLeagueImage()
            ->formatPathToTeamImages()
            ->formatPlayerData($fixture->playerInfos)
            ->addPlayerCountColumn()
            ->get();

        unset($fixture->playerInfos);

        $fixtureData = $fixture
            ->fixture
            ->mapWithKeys(function ($fixture, $key) {
                if ($key === "lineups") {
                    return [$key => $this->addPlayerGridCss($fixture)];
                }
                
                return [$key.'Data' => $fixture];
            })
            ->map(function ($data, $key) use ($fixture) {
                if ($key === "lineups") {
                    $players = $fixture->players->keyBy('player_info_id');
                    
                    $data['startXI'] = collect($data['startXI'])
                        ->map(function (array $startXIData) use ($players) {
                            return collect($startXIData)
                                ->map(function (array $playerData) use ($players) {
                                    return [
                                        'playerData' => $playerData,
                                        'player' => $players->get($playerData['id'])
                                    ];
                                });
                        });

                    $data['substitutes'] = collect($data['substitutes'])
                        ->map(function (array $substitutesData) use ($players) {
                            return collect($substitutesData)
                                ->map(function (array $playerData) use ($players) {
                                    return [
                                        'playerData' => $playerData,
                                        'player' => $players->get($playerData['id'])
                                    ];
                                });
                        });
                        
                    return $data;
                }

                return $data;
            });

        return collect([
            'fixtureId' => $fixture->id,
            'momCount' => $fixture->mom_count,
            'momLimit' => $fixture->momLimit,
            ...$fixtureData
        ]);
    }

    private function addPlayerGridCss(array $lineupsData): Collection
    {
        $count = $lineupsData['playerCount'];

        $countIsOne = $count === 1;
        
        return collect($lineupsData)
            ->put('playerGridCss', $countIsOne ? 'w-full' : 'w-1/'.$count);
    }
}