<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use App\Livewire\MessageType;
use App\Models\Player as EqPlayer;
use App\UseCases\User\Player\DecideManOfTheMatch;
use App\UseCases\User\Player\FetchMomCount;
use App\UseCases\User\Player\RatePlayer;
use App\UseCases\User\FixtureRequest;
use App\UseCases\User\Player\FindPlayer;
use Exception;
use Livewire\Attributes\On;
use Livewire\Component;


class Player extends Component
{
    public string $fixtureInfoId;
    public array $playerData;

    public string $name;
    public string $size;

    public ?EqPlayer $player;
    public ?float $rating;
    public bool $mom;
    public bool $canRate;
    public bool $canMom;
    public int $rateCount;
    public int $rateLimit;
    
    public int $momCount;
    public int $momLimit;

    public ?float $defaultRating;

    private readonly FindPlayer $findPlayer;
    private readonly RatePlayer $ratePlayer;
    private readonly RatingPresenter $presenter;
    private readonly DecideManOfTheMatch $decideMom;

    private const RATED_MESSAGE = 'Rated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';

    use PlayerTrait;
    use MomCountTrait;

    public function boot(
        RatePlayer $ratePlayer,
        DecideManOfTheMatch $decideMom,
        RatingPresenter $presenter)
    {
        $this->decideMom  = $decideMom;
        $this->ratePlayer = $ratePlayer;
        $this->presenter  = $presenter;
    }

    public function mount()
    {
        $this->defaultRating = (float) $this->playerData['defaultRating'];
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
            $request = FixtureRequest::make(
                    fixtureInfoId: $this->fixtureInfoId,
                    playerInfoId: $this->playerData['id']
                );
            
            $player = $this->ratePlayer->execute($request, $rating);
            
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
            $request = FixtureRequest::make(
                    fixtureInfoId: $this->fixtureInfoId,
                    playerInfoId: $this->playerData['id']
                );

            $players = $this->decideMom->execute($request);

            $this->dispatch('mom-count-updated');
            $this->dispatchPlayerUpdated($players['newMomPlayer']);
            $this->dispatchPlayerUpdated($players['oldMomPlayer']);
            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    private function dispatchPlayerUpdated(?EqPlayer $player)
    {
        if (!$player) return;

        $this->dispatch('update-player.'.$player->player_info_id, $player);
    }
}