<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Livewire\Component;


class Player extends Component
{
    public string $fixtureId;
    public array $player;

    public string $name;
    public string $size;

    use PlayerTrait;
    
    public function render()
    {
        return view('livewire.lineups.player');
    }
}