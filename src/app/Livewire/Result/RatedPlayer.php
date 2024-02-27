<?php declare(strict_types=1);

namespace App\Livewire\Result;

use App\Livewire\MessageType;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

use App\UseCases\Player\FetchPlayerUseCase;


class RatedPlayer extends Component
{
    public string $name;
    public string $fixtureId;
    public ?float $rating;
    public ?float $defaultRating;
    public array $player;
    public bool $mom;
    public bool $isRated;
    public string $size;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    
    public function boot(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mount()
    {
        $this->fetchPlayer($this->fixtureId, $this->player['id']);

        $this->defaultRating = (float) $this->player['defaultRating'];
    }

    public function render()
    {
        return view('livewire.result.rated-player');
    }
    
    /**
     * 対象のプレイヤーを取得する
     *
     * @return void
     */
    #[On('player-rated.{player.id}')]
    #[On('mom-decided.{player.id}')]
    #[On('mom-undecided.{player.id}')]
    public function fetchPlayer(): void
    {
        try {
            $player = $this->fetchPlayer->execute($this->fixtureId, $this->player['id']);
                        
            $this->rating = $player->rating;
            $this->mom    = $player->mom;
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}