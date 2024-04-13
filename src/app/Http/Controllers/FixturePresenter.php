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
                return [$key.'Data' => $fixture];
            })
            ->map(function ($data, $key) {
                if ($key === 'lineupsData') {
                    return $this->addPlayerGridCss($data);
                }

                return $data;
            });
        
        return collect([
            'fixtureId' => $fixture->id,
            'date' => $fixture->date,
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