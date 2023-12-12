<?php declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;


class PlayerDetail extends Component
{
    public Collection $players;

    public int $playerId;

    public $player;
    
    public function render()
    {
        return view('livewire.player-detail');
    }

    public function mount()
    {
        $this->player = $this
            ->players
            ->flatten()
            ->sole(fn($player) => $player->id === 987650);
    }

    #[On('player-selected')]
    public function playerSelected(int $playerId): void
    {
        $this->player = $this
            ->players
            ->flatten()
            ->sole(fn($player) => $player->id === $playerId);
    }
}
