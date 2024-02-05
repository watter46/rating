<?php declare(strict_types=1);

namespace App\Livewire\Rating;

use Exception;
use Livewire\Component;

use App\Models\Player;
use App\UseCases\Player\DecideManOfTheMatchUseCase;
use App\UseCases\Player\RatePlayerUseCase;
use App\UseCases\Player\FetchPlayerUseCase;


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

    private readonly FetchPlayerUseCase $fetchPlayer;
    private readonly RatePlayerUseCase $ratePlayer;
    private readonly DecideManOfTheMatchUseCase $decideMOM;

    public function boot(
        FetchPlayerUseCase $fetchPlayer,
        RatePlayerUseCase $ratePlayer,
        DecideManOfTheMatchUseCase $decideMOM)
    {
        $this->fetchPlayer = $fetchPlayer;
        $this->ratePlayer = $ratePlayer;
        $this->decideMOM = $decideMOM;
    }

    public function mount()
    {        
        $this->fetchPlayer();
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
            $player = $this->ratePlayer->execute($this->fixtureId, $this->playerId, $rating);
            
            $this->setProperty($player);

            $this->dispatch('player-rated', $this->playerId);
            $this->dispatch('notify', message: MessageType::Success->toArray(self::RATED_MESSAGE));

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
    
    /**
     * ManOfTheMatchを決める
     *
     * @return void
     */
    public function decideMOM()
    {
        try {
            $player = $this->decideMOM->execute($this->fixtureId, $this->playerId);

            $this->setProperty($player);

            $this->dispatch('player-mom-decided');
            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    /**
     * 対象のプレイヤーを取得する
     *
     * @return void
     */
    private function fetchPlayer(): void
    {
        try {
            $player = $this->fetchPlayer->execute($this->fixtureId, $this->playerId);
            
            $this->setProperty($player);

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
    
    /**
     * Propertyを設定する
     *
     * @param  ?Player $player
     * @return void
     */
    private function setProperty(?Player $player): void
    {
        $this->rating = $player->rating;
        $this->mom    = $player->mom;
        $this->canRate = $player->canRate;
    }
}