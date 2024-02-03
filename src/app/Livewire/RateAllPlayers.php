<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;


class RateAllPlayers extends Component
{
    public array $lineups;
    public string $fixtureId;

    public bool $isOpen = false;
    
    public function render()
    {
        return view('livewire.rate-all-players');
    }
}
