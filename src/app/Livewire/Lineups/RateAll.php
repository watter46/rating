<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Livewire\Component;


class RateAll extends Component
{
    public array $lineups;
    public string $fixtureId;

    public bool $isOpen = false;
    
    public function render()
    {
        return view('livewire.lineups.rate-all');
    }
}
