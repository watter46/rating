<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Exception;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Livewire\MessageType;
use App\UseCases\Player\CountRatedPlayerUseCase;


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
        return view('livewire.lineups.rated-count');
    }

    #[On('player-rated')]
    public function fetch(): void
    {
        try {
            $this->ratedCount = $this->countRatedPlayer->execute($this->fixtureId);
            $this->allRated   = $this->playerCount === $this->ratedCount;

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
