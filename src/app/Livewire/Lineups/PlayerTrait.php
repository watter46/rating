<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Exception;
use Livewire\Attributes\On;

use App\Livewire\MessageType;
use App\UseCases\User\Player\DecideManOfTheMatchUseCase;
use App\UseCases\User\Player\FetchPlayerUseCase;
use App\UseCases\User\Player\RatePlayerUseCase;
use App\UseCases\User\PlayerInFixtureRequest;


trait PlayerTrait
{
    public ?float $rating;
    public ?float $defaultRating;
    public bool $mom;
    public bool $canRate;

    private const RATED_MESSAGE = 'Rated!!';
    private const Decided_MOM_MESSAGE = 'Decided MOM!!';

    private readonly FetchPlayerUseCase $fetchPlayer;
    private readonly RatePlayerUseCase $ratePlayer;
    private readonly DecideManOfTheMatchUseCase $decideMOM;
    
    public function bootPlayerTrait(
        FetchPlayerUseCase $fetchPlayer,
        RatePlayerUseCase $ratePlayer,
        DecideManOfTheMatchUseCase $decideMOM)
    {
        $this->fetchPlayer = $fetchPlayer;
        $this->ratePlayer = $ratePlayer;
        $this->decideMOM = $decideMOM;
    }

    public function mountPlayerTrait()
    {
        $this->fetch();
        
        $this->defaultRating = $this->player['defaultRating'];
    }

    /**
     * 指定の選手を取得する
     *
     * @return void
     */
    #[On('fetch-player.{player.id}')]
    public function fetch(): void
    {
        $player = $this->fetchPlayer->execute(PlayerInFixtureRequest::make(
                fixtureId: $this->fixtureId,
                playerInfoId: $this->player['id']
            ));
        
        $this->rating  = $player->rating;
        $this->mom     = $player->mom;
        $this->canRate = $player->canRate;
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
                    playerInfoId: $this->player['id']
                );
            
            $this->ratePlayer->execute($request, $rating);
            
            $this->dispatchFetchPlayer($this->player['id']);
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
                    playerInfoId: $this->player['id']
                );

            $players = $this->decideMOM->execute($request);

            $this->dispatchFetchPlayer($players['newMomPlayerInfoId']);
            $this->dispatchFetchPlayer($players['oldMomPlayerInfoId']);
            $this->dispatch('notify', message: MessageType::Success->toArray(self::Decided_MOM_MESSAGE));
            $this->dispatch('close');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    private function dispatchFetchPlayer(?string $playerId): void
    {
        if (!$playerId) return;
        
        $this->dispatch("fetch-player.$playerId");
    }
}