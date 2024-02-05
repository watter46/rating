<?php declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

use App\UseCases\Player\FetchPlayerUseCase;


class Player extends Component
{
    public string $fixtureId;
    public array $player;
    public ?float $rating;
    public ?float $defaultRating;
    public bool $mom;
    public string $name;
    public bool $isRated;
    public bool $isUser = true;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function boot(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mount()
    {
        $this->fetchPlayer($this->fixtureId, $this->player['id']);

        $this->defaultRating = $this->player['defaultRating'];
    }
    
    public function render()
    {
        return view('livewire.player');
    }

    public function toDetail()
    {
        $this->dispatch('player-selected', $this->player['id']);
    }

    #[On('player-rated')]
    public function refetch(string $playerId): void
    {
        if ($playerId !== $this->player['id']) return;

        $this->fetchPlayer($this->fixtureId, $playerId);
    }
    
    #[On('player-mom-decided')]
    public function refetchAll(): void
    {
        $this->fetchPlayer($this->fixtureId, $this->player['id']);
    }

    #[On('user-machine-toggled')]
    public function toggle(bool $isUser)
    {
        $this->isUser = $isUser;
    }
    
    /**
     * ラストネームに変換する
     *
     * @return string
     */
    public function toLastName(): string
    {
        $shortName = Str::afterLast($this->player['name'], ' ');

        return $shortName;
    }
    
    /**
     * 対象のプレイヤーを取得する
     *
     * @param  ?string $playerId
     * @return void
     */
    private function fetchPlayer(string $fixtureId, string $playerId): void
    {
        try {
            if (!$playerId) return;

            $player = $this->fetchPlayer->execute($fixtureId, $playerId);
                        
            $this->rating = $player->rating;
            $this->mom    = $player->mom;
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
