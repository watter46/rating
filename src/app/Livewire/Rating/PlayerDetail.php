<?php declare(strict_types=1);

namespace App\Livewire\Rating;

use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;


class PlayerDetail extends Component
{
    public array $lineups;
    public string $fixtureId;
    public int $playerId;
    public array $player;
    
    public function render()
    {
        return view('livewire.rating.player-detail');
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

        $this->player = $lineups->sole(fn ($player) => $player['id'] === $playerId);
    }

    /**
     * ラストネームに変換する
     *
     * @return string
     */
    public function toLastName(): string
    {
        $shortName = Str::afterLast($this->player['name'], ' ');

        return $shortName;
    }
}
