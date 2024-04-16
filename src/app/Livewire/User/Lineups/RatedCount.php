<?php declare(strict_types=1);

namespace App\Livewire\User\Lineups;

use Exception;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Livewire\MessageType;
use App\Livewire\User\Data\FixtureDataPresenter;
use App\UseCases\User\Player\CountRatedPlayerUseCase;


class RatedCount extends Component
{   
    public string $fixtureId;

    public int  $ratedCount;
    public int  $playerCount;
    public int $ratedPercentage;
    public bool $allRated;
    public bool $isZero;

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
        return view('livewire.user.lineups.rated-count');
    }

    #[On('player-rated')]
    public function fetch(): void
    {
        try {
            $fixture = $this->countRatedPlayer->execute($this->fixtureId);
            
            $fixtureData = FixtureDataPresenter::create($fixture)
                ->playerCount()
                ->get();
            
            $this->ratedPercentage = $this->calculateRatedPercentage(
                    $fixture->ratedCount,
                    $fixtureData->get('playerCount')
                );
            
            $this->isZero = $this->ratedPercentage === 0;
                
            // $this->ratedCount  = $fixture->ratedCount;
            // $this->playerCount = $fixtureData->get('playerCount');
            // $this->allRated    = $this->playerCount === $this->ratedCount;

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    private function calculateRatedPercentage(int $ratedPlayerCount, int $totalPlayerCount): int
    {
        return (int) floor(($ratedPlayerCount / $totalPlayerCount) * 100);
    }
}
