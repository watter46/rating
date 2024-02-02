<?php declare(strict_types=1);

namespace App\Livewire;

use App\UseCases\Player\CountRatedPlayerUseCase;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;

class RatedCount extends Component
{   
    public string $fixtureId;

    public int $ratedCount;
    public int $playerCount;
    public bool $allRated;

    private readonly CountRatedPlayerUseCase $countRatedPlayer;
    
    public function boot(CountRatedPlayerUseCase $countRatedPlayer)
    {
        $this->countRatedPlayer = $countRatedPlayer;
    }

    public function mount()
    {
        $this->fetch();
    }
    
    public function render()
    {
        return view('livewire.rated-count');
    }

    #[On('player-rated')]
    public function fetch()
    {
        try {
            $this->ratedCount = $this->countRatedPlayer->execute($this->fixtureId);
            $this->allRated   = $this->playerCount === $this->ratedCount;

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
