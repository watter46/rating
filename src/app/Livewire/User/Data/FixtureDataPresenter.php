<?php declare(strict_types=1);

namespace App\Livewire\User\Data;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Models\Fixture;


readonly class FixtureDataPresenter
{
    private function __construct(private Collection $fixtureData)
    {
        //
    }
    
    public static function create(Fixture $fixture)
    {
        return new self($fixture->fixture);
    }

    public function get(): Collection
    {
        return $this->fixtureData;
    }
        
    /**
     * 出場した選手数をカウントする
     *
     * @return self
     */
    public function playerCount(): self
    {
        $count = $this->fixtureData
            ->dataGet('lineups')
            ->flatten(1)
            ->count();

        $formatted = $this->fixtureData->dataSet('playerCount', $count);

        return new self($formatted);
    }
    
    /**
     * 先発出場した選手を表示用に成形する
     *
     * @return self
     */
    public function formatFormation(): self
    {        
        $startXI = collect($this->fixtureData->dataGet('lineups.startXI'))
            ->reverse()
            ->groupBy(function ($player) {
                return Str::before($player['grid'], ':');
            })
            ->values();

        $formatted = $this->fixtureData->dataSet('lineups.startXI', $startXI);

        return new self($formatted);
    }
    
    /**
     * 控えの選手を表示用に成形する
     *
     * @return self
     */
    public function formatSubstitutes(): self
    {
        $substitutesData = collect($this->fixtureData->dataGet('lineups.substitutes'));

        $substitutes = SubstitutesSplitter::split($substitutesData)->get();

        $formatted = $this->fixtureData->dataSet('lineups.substitutes', $substitutes);

        return new self($formatted);
    }
}