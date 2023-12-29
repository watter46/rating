<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

use App\UseCases\Player\FetchPlayerUseCase;


class Player extends Component
{
    public $player;

    public float $rating;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function boot(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mount()
    {
        $this->fetchPlayer($this->player['id']);
    }
    
    public function render()
    {
        return view('livewire.player');
    }

    public function toDetail(string $playerId)
    {
        $this->dispatch('player-selected', $playerId);
    }

    #[On('player-evaluated')]
    public function refetch(string $playerId): void
    {
        if ($playerId !== $this->player['id']) return;

        $this->fetchPlayer($playerId);
    }
    
    /**
     * 対象のプレイヤーを取得する
     *
     * @param  string $playerId
     * @return void
     */
    private function fetchPlayer(string $playerId): void
    {
        $player = $this->fetchPlayer->execute($playerId);
        
        $this->rating = $player->rating;
    }
}
