<?php declare(strict_types=1);

namespace App\Livewire\Fixtures;

use Illuminate\Support\Collection;
use Livewire\Component;

class Score extends Component
{
    public string $fixtureId;
    public Collection $fixture;
    public Collection $score;
    public ?bool $winner;
    public bool $isRate;

    public function render()
    {
        return view('livewire.fixtures.score');
    }

    public function toFixture(): void
    {
        $this->redirect("/fixtures/$this->fixtureId");
    }
}