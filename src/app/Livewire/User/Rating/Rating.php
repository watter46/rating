<?php declare(strict_types=1);

namespace App\Livewire\User\Rating;

use Exception;
use Livewire\Component;

use App\Livewire\MessageType;
use App\Livewire\User\Lineups\MomCountTrait;
use App\Livewire\User\Lineups\PlayerTrait;
use App\UseCases\User\Player\DecideManOfTheMatchUseCase;
use App\UseCases\User\Player\RatePlayerUseCase;
use App\UseCases\User\PlayerInFixtureRequest;


class Rating extends Component
{    
    public array $playerData;
    public string $fixtureId;

    private readonly RatePlayerUseCase $ratePlayer;
    private readonly RatingPresenter $presenter;
    private readonly DecideManOfTheMatchUseCase $decideMOM;

    private const RATED_MESSAGE = 'Rated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';
    
    use PlayerTrait;
    use MomCountTrait;
    
    public function boot(
        RatePlayerUseCase $ratePlayer,
        DecideManOfTheMatchUseCase $decideMOM,
        RatingPresenter $presenter)
    {
        $this->decideMOM  = $decideMOM;
        $this->ratePlayer = $ratePlayer;
        $this->presenter  = $presenter;
    }
    
    public function render()
    {
        $presenter = $this->presenter->create(
                $this->rateCount,
                $this->rateLimit,
                $this->momLimit,
                $this->momCount
            );

        return view('livewire.user.rating.rating', [
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
            $request = PlayerInFixtureRequest::make(
                    fixtureId: $this->fixtureId,
                    playerInfoId: $this->playerData['id']
                );
            
            $this->ratePlayer->execute($request, $rating);
                        
            $this->dispatch('fetch-player.'.$this->playerData['id']);
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
    public function decideMOM(): void
    {
        try {
            $request = PlayerInFixtureRequest::make(
                    fixtureId: $this->fixtureId,
                    playerInfoId: $this->playerData['id']
                );

            $players = $this->decideMOM->execute($request);

            $this->dispatchMomCount();
            $this->dispatch('fetch-player.'.$players['newMomPlayerInfoId']);
            $this->dispatch('fetch-player.'.$players['oldMomPlayerInfoId']);
            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}