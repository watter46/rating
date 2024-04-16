<?php declare(strict_types=1);

namespace App\Livewire\User\Lineups;

use Exception;
use Livewire\Attributes\On;

use App\Livewire\MessageType;
use App\UseCases\User\Player\FetchPlayerUseCase;
use App\UseCases\User\PlayerInFixtureRequest;


trait PlayerTrait
{
    public ?float $rating;
    public ?float $defaultRating;
    public int $rateCount;
    public int $rateLimit;
    public bool $mom;
    public bool $canRate;
    public bool $canMom;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function bootPlayerTrait(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mountPlayerTrait()
    {
        $this->fetch();
    }

    /**
     * 指定の選手を取得する
     *
     * @return void
     */
    #[On('fetch-player.{playerData.id}')]
    public function fetch(): void
    {
        try {
            $player = $this->fetchPlayer->execute(PlayerInFixtureRequest::make(
                    fixtureId: $this->fixtureId,
                    playerInfoId: $this->playerData['id']
                ));

            $this->rateCount = $player->rate_count;
            $this->rateLimit = $player->rateLimit;
            $this->rating = $player->rating;
            $this->defaultRating = $player->defaultRating;
            $this->mom = $player->mom;
            $this->canRate = $player->canRate;
            $this->canMom = $player->canMom;
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}