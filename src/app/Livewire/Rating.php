<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

use App\UseCases\Player\EvaluatePlayerUseCase;
use App\UseCases\Player\FetchPlayerUseCase;


class Rating extends Component
{
    public string $playerId;
    public float $defaultRating;
    public float $rating;

    private FetchPlayerUseCase $fetchPlayer;
    private readonly EvaluatePlayerUseCase $evaluatePlayer;

    public function boot(FetchPlayerUseCase $fetchPlayer, EvaluatePlayerUseCase $evaluatePlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
        $this->evaluatePlayer = $evaluatePlayer;
    }

    public function mount()
    {
        $this->fetchPlayer($this->playerId);
    }

    public function render()
    {
        return view('livewire.rating');
    }

    public function evaluate(string $playerId, float $rating): void
    {
        $this->evaluatePlayer->execute($playerId, $rating);

        $this->fetchPlayer($playerId);

        $this->dispatch('player-evaluated', $playerId);
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
