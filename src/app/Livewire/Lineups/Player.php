<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Exception;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Livewire\MessageType;
use App\UseCases\Player\FetchPlayerUseCase;


class Player extends Component
{
    public string $fixtureId;
    public array $player;
    public ?float $rating;
    public ?float $defaultRating;
    public bool $mom;
    public bool $isRated;
    public bool $isUser = true;

    public string $name;
    public string $size;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function boot(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mount()
    {
        $this->handleFetchPlayerEvent();

        $this->defaultRating = $this->player['defaultRating'];
    }
    
    public function render()
    {
        return view('livewire.lineups.player');
    }

    #[On('user-machine-toggled')]
    public function toggle(bool $isUser)
    {
        $this->isUser = $isUser;
    }

    /**
     * Playerを取得後にPlayerFetchedイベントを発行する
     *
     * @return void
     */
    #[On('fetch-player.{player.id}')]    
    public function handleFetchPlayerEvent(): void
    {
        try {
            $player = $this->fetchPlayer->execute($this->fixtureId, $this->player['id']);

            $this->dispatch('player-fetched.'.$this->player['id'], $player);

            $this->rating = $player['rating'];
            $this->mom    = $player['mom'];
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    #[On('fetch-all-player')]
    public function handleFetchAllPlayerEvent(array $playerIds)
    {
        collect($playerIds)
            ->each(function (string $id) {
                $this->dispatch("fetch-player.$id")->self();
            });
    }
}