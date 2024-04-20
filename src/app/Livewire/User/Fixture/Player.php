<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use App\Livewire\MessageType;
use App\Models\Player as EqPlayer;
use App\UseCases\User\Player\DecideManOfTheMatchUseCase;
use App\UseCases\User\Player\FetchMomCountUseCase;
use App\UseCases\User\Player\RatePlayerUseCase;
use App\UseCases\User\PlayerInFixtureRequest;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;


class Player extends Component
{
    public string $fixtureId;
    public array $playerData;

    public string $name;
    public string $size;

    public EqPlayer $player;
    public int $momCount;
    public int $momLimit;

    public ?float $defaultRating;

    private readonly RatePlayerUseCase $ratePlayer;
    private readonly RatingPresenter $presenter;
    private readonly DecideManOfTheMatchUseCase $decideMOM;
    private readonly FetchMomCountUseCase $fetchMomCount;

    private const RATED_MESSAGE = 'Rated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';

    use PlayerTrait;

    public function boot(
        RatePlayerUseCase $ratePlayer,
        DecideManOfTheMatchUseCase $decideMOM,
        FetchMomCountUseCase $fetchMomCount,
        RatingPresenter $presenter)
    {
        $this->decideMOM  = $decideMOM;
        $this->ratePlayer = $ratePlayer;
        $this->fetchMomCount = $fetchMomCount;
        $this->presenter  = $presenter;
    }

    public function mount()
    {
        $this->defaultRating = $this->playerData['defaultRating'];
    }
    
    public function render()
    {
        $presenter = $this->presenter->create(
            $this->rateCount,
            $this->rateLimit,
            $this->momLimit,
            $this->momCount
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
            $request = PlayerInFixtureRequest::make(
                    fixtureId: $this->fixtureId,
                    playerInfoId: $this->playerData['id']
                );
            
            $this->ratePlayer->execute($request, $rating);
            
            $this->dispatchPlayerFetched($this->playerData['id']);
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

            $this->dispatch('mom-count-updated');
            $this->dispatchPlayerFetched($players['newMomPlayerInfoId']);
            $this->dispatchPlayerFetched($players['oldMomPlayerInfoId']);
            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    #[On('mom-count-updated')]
    public function updateMomCount(): void
    {
        $request = PlayerInFixtureRequest::make(fixtureId: $this->fixtureId);

        ['momLimit' => $this->momLimit, 'mom_count' => $this->momCount, 'exceedMomLimit' => $exceedMomLimit]
            = $this->fetchMomCount->execute($request);

        if ($exceedMomLimit) {
            $this->dispatch('mom-button-disabled');
        }
    }
    
    /**
     * 指定の選手を取得するイベントを発行する
     *
     * @param  string $playerInfoId
     * @return void
     */
    private function dispatchPlayerFetched(string $playerInfoId): void
    {
        $this->dispatch('fetch-player.'.$playerInfoId);
    }
}