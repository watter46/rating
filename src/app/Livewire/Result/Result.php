<?php declare(strict_types=1);

namespace App\Livewire\Result;

use Livewire\Component;

class Result extends Component
{
    public array $fixture;
    public array $teams;
    public array $league;
    public array $score;
    public array $lineups;
    public string $fixtureId;

    public function render()
    {
        return view('livewire.result.result');
    }
}