<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use App\Livewire\MessageType;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Support\Str;

use App\UseCases\Player\FetchPlayersUseCase;
use App\UseCases\Player\RateAllPlayersUseCase;
use App\Livewire\RateAllPlayersResource;


class RateAllPlayers extends Component
{
    public array $lineups;
    public string $fixtureId;

    public Collection $players;
    public bool $canRated;

    private readonly RateAllPlayersUseCase $rateAllPlayers;
    private readonly FetchPlayersUseCase $fetchPlayers;
    private readonly RateAllPlayersResource $resource;

    private const RATED_MESSAGE = 'Rated!!';
    
    public function boot(
        RateAllPlayersUseCase $rateAllPlayers,
        FetchPlayersUseCase $fetchPlayers,
        RateAllPlayersResource $resource)
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
        return view('livewire.lineups.rate-all-players');
    }

    /**
     * ラストネームに変換する
     * @param string $name
     * @return string
     */
    public function toLastName(string $name): string
    {
        $shortName = Str::afterLast($name, ' ');

        return $shortName;
    }
    
    /**
     * 試合に出場したプレイヤー全てを取得する
     *
     * @return void
     */
    private function fetch(): void
    {
        try {
            $playerInfoIdList = $this->resource
                ->lineupsToPlayers($this->lineups)
                ->pluck('id');

            $data = $this->fetchPlayers->execute($playerInfoIdList, $this->fixtureId);

            $this->players  = $this->resource->format($this->lineups, $data->get('players'));
            $this->canRated = $data->get('canRated');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
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
            $this->rateAllPlayers->execute($this->fixtureId, collect($players));

            $this->dispatch('player-mom-decided');
            $this->dispatch('notify', message: MessageType::Success->toArray(self::RATED_MESSAGE));

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
