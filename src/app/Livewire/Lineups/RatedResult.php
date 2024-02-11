<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Livewire\Component;

class RatedResult extends Component
{
    public array $fixture;
    public array $teams;
    public array $league;
    public array $score;
    public array $lineups;
    public string $fixtureId;

    public bool $isOpen = false;

    public function render()
    {
        return view('livewire.lineups.rated-result');
    }
}