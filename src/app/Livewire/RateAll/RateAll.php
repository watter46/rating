<?php declare(strict_types=1);

namespace App\Livewire\RateAll;

use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\On;

use App\Livewire\MessageType;
use App\Livewire\RateAll\RateAllResource;
use App\UseCases\Player\FetchPlayersUseCase;
use App\UseCases\Player\RateAllPlayersUseCase;


class RateAll extends Component
{
    public array $lineups;
    public string $fixtureId;

    public Collection $profiles;
    public Collection $players;

    private readonly RateAllPlayersUseCase $rateAllPlayers;
    private readonly FetchPlayersUseCase $fetchPlayers;
    private readonly RateAllResource $resource;

    private const RATED_MESSAGE = 'Rated!!';
    private const VALIDATE_ERROR = 'Some players are not evaluated.';
    
    public function boot(
        RateAllPlayersUseCase $rateAllPlayers,
        FetchPlayersUseCase $fetchPlayers,
        RateAllResource $resource)
    {
        $this->rateAllPlayers = $rateAllPlayers;
        $this->fetchPlayers = $fetchPlayers;
        $this->resource = $resource;
    }
    
    public function mount()
    {
        $this->fetch();
    }

    public function render()
    {
        return view('livewire.rate-all.rate-all');
    }
    
    /**
     * 試合に出場したプレイヤー全てを取得する
     *
     * @return void
     */
    private function fetch(): void
    {
        try {
            $playerInfoIds = $this->resource
                ->lineupsToPlayers($this->lineups)
                ->pluck('id');
                
            $data = $this->fetchPlayers->execute($playerInfoIds, $this->fixtureId);

            $playersData = $this->resource->format($this->lineups, $data->get('players'));
            
            $this->profiles = $playersData->get('profiles');
            $this->players  = $playersData->get('players');
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    #[On('modal-opened-rate-all')]
    public function refetch(): void
    {        
        $this->fetch();
    }
    
    /**
     * すべてのプレイヤーを評価する
     *
     * @param  array $players
     * @return void
     */
    public function rateAll(array $players): void
    {
        try {
            $validated = collect($players)->every(fn(array $player) => $player['rating']);
            
            if (!$validated) {
                throw new Exception(self::VALIDATE_ERROR);
            }

            $this->rateAllPlayers->execute($this->fixtureId, collect($players));

            $this->dispatch('notify', message: MessageType::Success->toArray(self::RATED_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
