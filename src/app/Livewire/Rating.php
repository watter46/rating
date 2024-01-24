<?php declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Livewire\Component;

use App\Models\Player;
use App\UseCases\Player\DecideManOfTheMatchUseCase;
use App\UseCases\Player\EvaluatePlayerUseCase;
use App\UseCases\Player\FetchPlayerUseCase;


class Rating extends Component
{
    private const Evaluated_MESSAGE = 'Evaluated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';
    
    public string $playerId;
    public string $fixtureId;
    public ?float $defaultRating;
    public ?float $rating;
    public bool $mom;

    private FetchPlayerUseCase $fetchPlayer;
    private readonly EvaluatePlayerUseCase $evaluatePlayer;
    private readonly DecideManOfTheMatchUseCase $decideMOM;

    public function boot(
        FetchPlayerUseCase $fetchPlayer,
        EvaluatePlayerUseCase $evaluatePlayer,
        DecideManOfTheMatchUseCase $decideMOM)
    {
        $this->fetchPlayer = $fetchPlayer;
        $this->evaluatePlayer = $evaluatePlayer;
        $this->decideMOM = $decideMOM;
    }

    public function mount()
    {        
        $this->fetchPlayer($this->fixtureId, $this->playerId);
    }

    public function render()
    {
        return view('livewire.rating');
    }
    
    /**
     * 選手のレートを評価する
     *
     * @param  float $rating
     * @return void
     */
    public function evaluate(float $rating): void
    {
        try {
            $player = $this->evaluatePlayer->execute($this->fixtureId, $this->playerId, $rating);

            $this->setProperty($player);

            $this->dispatch('player-evaluated', $this->playerId);
            $this->dispatch('notify', message: self::Evaluated_MESSAGE);

        } catch (Exception $e) {
            dd($e);
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
            $this->dispatch('notify', message: self::Decided_MOM_MESSAGE);

        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * 対象のプレイヤーを取得する
     *
     * @param  string $fixtureId
     * @param  string $playerId
     * @return void
     */
    private function fetchPlayer(string $fixtureId, string $playerId): void
    {
        try {
            $player = $this->fetchPlayer->execute($fixtureId, $playerId);
        
            $this->setProperty($player);

        } catch (Exception $e) {
            dd($e);
        }
    }
    
    /**
     * Propertyを設定する
     *
     * @param  ?Player $player
     * @return void
     */
    private function setProperty(?Player $player)
    {
        if (!$player) {
            $this->rating = $this->defaultRating;
            $this->mom = false;
            return;
        }

        $this->rating = $player->rating ?? $this->defaultRating;
        $this->mom = $player->mom ?? false;
    }
}
