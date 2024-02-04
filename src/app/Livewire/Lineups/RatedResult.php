<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Livewire\Component;

class RatedResult extends Component
{
    public array $lineups;
    public array $player;
    public string $fixtureId;

    public bool $isOpen;

    public function render()
    {
        return view('livewire.lineups.rated-result');
    }
}