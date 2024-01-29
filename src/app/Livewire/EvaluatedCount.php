<?php declare(strict_types=1);

namespace App\Livewire;

use App\UseCases\Player\CountEvaluatedPlayerUseCase;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;

class EvaluatedCount extends Component
{   
    public string $fixtureId;

    public int $evaluatedCount;
    public int $playerCount;
    public bool $allEvaluated;

    private readonly CountEvaluatedPlayerUseCase $countEvaluatedPlayer;
    
    public function boot(CountEvaluatedPlayerUseCase $countEvaluatedPlayer)
    {
        $this->countEvaluatedPlayer = $countEvaluatedPlayer;
    }

    public function mount()
    {
        $this->fetch();
    }
    
    public function render()
    {
        return view('livewire.evaluated-count');
    }

    #[On('player-evaluated')]
    public function fetch()
    {
        try {
            $this->evaluatedCount = $this->countEvaluatedPlayer->execute($this->fixtureId);
            $this->allEvaluated   = $this->playerCount === $this->evaluatedCount;

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
