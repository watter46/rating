<?php declare(strict_types=1);

namespace App\Http\Controllers\Presenters;

use App\Livewire\Admin\Player;
use Illuminate\Support\Collection;


class FixturePresenter
{
    public function __construct(private Collection $fixture)
    {
        
    }

    public function format()
    {
        $presenter = new FixtureInfoPresenter($this->fixture['fixture_info']);
        
        // Playerkara
        
        
        return dd([
            'fixtureInfoId' => $this->fixture['fixture_info_id'],
            'momCount'      => $this->fixture['mom_count'],
            'momLimit'      => $this->fixture['mom_limit'],
            'score'         => $presenter->formatScore(),
            'teams'         => $presenter->formatTeams(),
            'league'        => $presenter->formatLeague(),
            'fixture'       => $presenter->formatFixture(),
            'lineups'       => $presenter->formatLineups()
        ]);
    }
}