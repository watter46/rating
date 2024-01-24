<?php declare(strict_types=1);

namespace App\Livewire;

use App\UseCases\Player\DecideManOfTheMatchUseCase;
use Livewire\Component;

use App\UseCases\Player\EvaluatePlayerUseCase;
use App\UseCases\Player\FetchPlayerUseCase;
use Exception;

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
        $this->evaluatePlayer->execute($this->fixtureId, $this->playerId, $rating);

        $this->dispatch('player-evaluated', $this->playerId);
        $this->dispatch('notify', message: self::Evaluated_MESSAGE);
    }
    
    /**
     * ManOFTheMatchを決める
     *
     * @return void
     */
    public function decideMOM()
    {
        try {
            $this->decideMOM->execute($this->fixtureId, $this->playerId);

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
        $player = $this->fetchPlayer->execute($fixtureId, $playerId);
        
        if (!$player) {
            $this->rating = $this->defaultRating;
            $this->mom = false;
            return;
        }

        $this->rating = $player->rating ?? $this->defaultRating;
        $this->mom = $player->mom;
    }
}
