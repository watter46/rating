<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Exception;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Livewire\MessageType;
use App\UseCases\User\Player\CountRatedPlayer;


class RatedCount extends Component
{
    public string $fixtureInfoId;

    public int $ratedPercentage;
    public bool $isZero;

    private readonly CountRatedPlayer $countRatedPlayer;
    
    public function boot(CountRatedPlayer $countRatedPlayer)
    {
        $this->countRatedPlayer = $countRatedPlayer;
    }

    public function mount()
    {
        $this->fetch();
    }
    
    public function render()
    {
        return view('livewire.user.fixture.rated-count');
    }

    #[On('player-rated')]
    public function fetch(): void
    {
        try {
            $fixture = $this->countRatedPlayer->execute($this->fixtureInfoId);
            
            $this->ratedPercentage = $this->calculateRatedPercentage(...$fixture);
            
            $this->isZero = $this->ratedPercentage === 0;

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    private function calculateRatedPercentage(int $ratedCount, int $playerCount): int
    {
        return (int) floor(($ratedCount / $playerCount) * 100);
    }
}
