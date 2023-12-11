<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;


class Player extends Component
{
    public $player;
    
    public function render()
    {
        return view('livewire.player');
    }

    public function toDetail(int $playerId)
    {
        $this->dispatch('player-selected', $playerId);
    }

    #[On('player-evaluate')]
    public function evaluate(int $playerId, float $rating): void
    {
        dd($playerId, $rating);
    }
}
