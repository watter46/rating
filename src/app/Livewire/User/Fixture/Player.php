<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Exception;
use Livewire\Component;

use App\Livewire\MessageType;
use App\UseCases\User\Player\DecideManOfTheMatch;
use App\UseCases\User\Player\RatePlayer;
use App\UseCases\User\Player\FindPlayer;


class Player extends Component
{
    public string $name;
    public string $size;

    public array $player;

    private readonly FindPlayer $findPlayer;
    private readonly RatePlayer $ratePlayer;
    private readonly RatingPresenter $presenter;
    private readonly DecideManOfTheMatch $decideMom;

    private const RATED_MESSAGE = 'Rated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';

    use PlayerTrait;

    public function boot(
        RatePlayer $ratePlayer,
        DecideManOfTheMatch $decideMom,
        RatingPresenter $presenter)
    {
        $this->decideMom  = $decideMom;
        $this->ratePlayer = $ratePlayer;
        $this->presenter  = $presenter;
    }

    public function render()
    {
        $presenter = $this->presenter->create(
                $this->player['rateCount'],
                $this->player['rateLimit'],
                $this->player['momLimit'],
                $this->player['momCount']
            );

        return view('livewire.user.fixture.player', [
            'rateCountRange' => $presenter->getRateCountRange(),
            'remainingRateCountRange' => $presenter->getRemainingRateCountRange(),
            'momCountRange' => $presenter->getMomCountRange(),
            'remainingMomCountRange' => $presenter->getRemainingMomCountRange()
        ]);
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
            $player = $this->ratePlayer->execute(
                    $this->player['fixture_info_id'],
                    $this->player['player_info_id'],
                    $rating
                );
            
            $this->dispatchPlayerUpdated($player);
            $this->dispatch('player-rated');
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
    public function decideMom(): void
    {
        try {
            [$newMom, $oldMom] = $this->decideMom->execute(
                    $this->player['fixture_info_id'],
                    $this->player['player_info_id']
                );

            $this->dispatch('mom-count-updated', $newMom);
            $this->dispatchPlayerUpdated($newMom);
            $this->dispatchPlayerUpdated($oldMom);
            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}