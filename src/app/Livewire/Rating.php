<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

use App\UseCases\Player\EvaluatePlayerUseCase;
use App\UseCases\Player\FetchPlayerUseCase;


class Rating extends Component
{
    public string $playerId;
    public string $fixtureId;
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
        $this->fetchPlayer($this->fixtureId, $this->playerId);
    }

    public function render()
    {
        return view('livewire.rating');
    }

    public function evaluate(string $fixtureId, string $playerId, float $rating): void
    {
        $this->evaluatePlayer->execute($fixtureId, $playerId, $rating);

        $this->fetchPlayer($fixtureId, $playerId);

        $this->dispatch('player-evaluated', $playerId);
    }

    /**
     * 対象のプレイヤーを取得する
     *
     * @param  string $fixtureId
     * @param  string $playerId
     * @return void
     */
    private function fetchPlayer(string $fixtureId, string $playerId): void
    {
        $player = $this->fetchPlayer->execute($fixtureId, $playerId);

        if (!$player) {
            $this->rating = $this->defaultRating;
            return;
        }
        
        $this->rating = $player->rating;
    }
}
