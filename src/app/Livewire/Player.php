<?php declare(strict_types=1);

namespace App\Livewire;

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

    public bool $isEvaluated;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function boot(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mount()
    {
        $this->fetchPlayer($this->fixtureId, $this->player['id']);
    }
    
    public function render()
    {
        return view('livewire.player');
    }

    public function toDetail()
    {
        $this->dispatch('player-selected', $this->player['id']);
    }

    #[On('player-evaluated')]
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
        if (!$playerId) return;

        $player = $this->fetchPlayer->execute($fixtureId, $playerId);
        
        $this->defaultRating = (float) $this->player['defaultRating'];
        
        if (!$player) {
            $this->rating = $this->defaultRating;
            $this->isEvaluated = false;
            return;
        }
        
        $this->rating = $player->rating ?? $this->defaultRating;
        $this->isEvaluated = true;
        $this->mom = $player->mom;
    }
}
