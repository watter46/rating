<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Livewire\User\Data\FixtureDataPresenter;


final readonly class FixturePresenter
{
    public function format(Fixture $fixture): Fixture
    {
        $fixture->fixture = FixtureDataPresenter::create($fixture)
            ->formatFormation()
            ->formatSubstitutes()
            ->formatPathToLeagueImage()
            ->formatPathToTeamImages()
            ->formatPlayerData($fixture->playerInfos)
            ->get();
        
        unset($fixture->playerInfos);
        
        return $fixture;
    }
}