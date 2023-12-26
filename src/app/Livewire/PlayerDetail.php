<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;


class PlayerDetail extends Component
{
    public array $lineups;

    public string $modelId;

    public int $playerId;

    public $player;
    
    public function render()
    {
        return view('livewire.player-detail');
    }

    public function mount()
    {
        $this->player = collect($this->lineups['startXI'])
            ->flatten(1)
            ->first();
    }

    #[On('player-selected')]
    public function playerSelected(int $playerId): void
    {
        $this->player = collect($this->lineups['startXI'])
            ->flatten(1)
            ->sole(fn ($player) => $player['id'] === $playerId);
    }
}
