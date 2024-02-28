<?php declare(strict_types=1);

namespace App\Livewire\Rating;

use Exception;
use Livewire\Component;
use Livewire\Attributes\On;

use App\Livewire\MessageType;
use App\UseCases\Player\DecideManOfTheMatchUseCase;
use App\UseCases\Player\RatePlayerUseCase;


class Rating extends Component
{
    private const RATED_MESSAGE = 'Rated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';
    
    public string $playerId;
    public string $fixtureId;
    public ?float $defaultRating;
    public ?float $rating;
    public bool $mom;
    public bool $canRate;

    private readonly RatePlayerUseCase $ratePlayer;
    private readonly DecideManOfTheMatchUseCase $decideMOM;

    public function boot(
        RatePlayerUseCase $ratePlayer,
        DecideManOfTheMatchUseCase $decideMOM)
    {
        $this->ratePlayer = $ratePlayer;
        $this->decideMOM = $decideMOM;
    }

    public function mount()
    {        
        $this->dispatchFetchPlayer($this->playerId);
    }

    public function render()
    {
        return view('livewire.rating.rating');
    }
    
    /**
     * 選手のレートを評価する
     *
     * @param  float $rating
     * @return void
     */
    public function rate(float $rating): void
    {
        try {
            $this->ratePlayer->execute($this->fixtureId, $this->playerId, $rating);
            
            $this->dispatch("fetch-player.$this->playerId");
            $this->dispatch('notify', message: MessageType::Success->toArray(self::RATED_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
    
    /**
     * ManOfTheMatchを決める
     *
     * @return void
     */
    public function decideMOM(): void
    {
        try {
            $players = $this->decideMOM->execute($this->fixtureId, $this->playerId);

            $this->dispatchFetchPlayer($players['newMomId']);
            $this->dispatchFetchPlayer($players['oldMomId']);

            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    /**
     * Playerを取得するイベントを発行する
     *
     * @return void
     */
    private function dispatchFetchPlayer(string $playerId): void
    {
        $this->dispatch("fetch-player.$playerId");
    }

    /**
     * Playerイベントから値をセットする
     *
     * @param  array $player
     * @return void
     */
    #[On('player-fetched.{playerId}')]
    public function handlePlayerEvent(array $player)
    {
        $this->rating  = $player['rating'];
        $this->mom     = $player['mom'];
        $this->canRate = $player['canRate'];
    }
}