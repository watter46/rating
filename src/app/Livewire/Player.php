<?php declare(strict_types=1);

namespace App\Livewire;

use App\UseCases\Player\EvaluatePlayerUseCase;
use Livewire\Attributes\On;
use Livewire\Component;


class Player extends Component
{
    public $player;

    private readonly EvaluatePlayerUseCase $evaluatePlayer;
    
    public function boot(EvaluatePlayerUseCase $evaluatePlayer)
    {
        $this->evaluatePlayer = $evaluatePlayer;
    }
    
    public function render()
    {
        return view('livewire.player');
    }

    public function toDetail(int $playerId)
    {
        $this->dispatch('player-selected', $playerId);
    }

    #[On('player-evaluate')]
    public function evaluate(string $fixtureId, int $playerId, float $rating): void
    {
        $this->evaluatePlayer->execute($fixtureId, $playerId, $rating);
    }
}
