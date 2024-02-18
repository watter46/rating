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
        $this->fetch();

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
     * 対象のプレイヤーを取得する
     *
     * @return void
     */
    #[On('player-rated.{player.id}')]
    #[On('mom-decided.{player.id}')]
    #[On('mom-undecided.{player.id}')]
    public function fetch(): void
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
