<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;


class PlayerDetail extends Component
{
    public array $lineups;

    public string $fixtureId;

    public int $playerId;

    public array $player;

    public function mount()
    {
        // $lineups = collect($this->lineups)
        //     ->map(function ($lineups, $key) {
        //         if ($key === 'startXI') {
        //             return collect($lineups)->flatten(1);
        //         }

        //         return $lineups;
        //     })
        //     ->flatten(1);

        // $this->player = $lineups
        //     ->sole(fn ($player) => $player['id'] === '01hjtx4yspw6hqd6wr1h778a07');
    }
    
    public function render()
    {
        return view('livewire.player-detail');
    }

    #[On('player-selected')]
    public function playerSelected(string $playerId): void
    {
        $lineups = collect($this->lineups)
            ->map(function ($lineups, $key) {
                if ($key === 'startXI') {
                    return collect($lineups)->flatten(1);
                }

                return $lineups;
            })
            ->flatten(1);

        $this->player = $lineups
            ->sole(fn ($player) => $player['id'] === $playerId);
    }

    #[On('player-evaluated')]
    public function hidden()
    {
        $this->player = [];
    }
}
