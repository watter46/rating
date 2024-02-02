<?php declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

use App\UseCases\Player\FetchPlayerUseCase;


class EvaluatedPlayer extends Component
{
    public string $name;
    public string $fixtureId;
    public ?float $rating;
    public ?float $defaultRating;
    public array $player;
    public bool $mom;
    public bool $isEvaluated;

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
        return view('livewire.evaluated-player');
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
