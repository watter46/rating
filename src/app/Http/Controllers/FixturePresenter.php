<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Livewire\User\Data\FixtureDataPresenter;


final readonly class FixturePresenter
{    
    public function format(Fixture $fixture): Collection
    {        
        $newFixture = FixtureDataPresenter::create(collect($fixture)->toCollection())
            ->formatFormation()
            ->formatSubstitutes()
            ->formatPlayerData($fixture->fixtureInfo->playerInfos)
            ->addPlayerCountColumn()
            ->get()
            ->dd();

        $lineupsData = $this->addPlayerGridCss($newFixture->fixtureInfo->lineups);
            
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