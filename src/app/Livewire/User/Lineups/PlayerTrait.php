<?php declare(strict_types=1);

namespace App\Livewire\User\Lineups;

use App\Livewire\User\Rating\Rating;
use Livewire\Attributes\On;

use App\UseCases\User\Player\FetchPlayerUseCase;
use App\UseCases\User\PlayerInFixtureRequest;
use Exception;

trait PlayerTrait
{
    public ?float $rating;
    public ?float $defaultRating;
    public bool $mom;
    public bool $canRate;
    public int $momCount;

    public array $player;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function bootPlayerTrait(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mountPlayerTrait()
    {
        $this->fetch();
        
        $this->defaultRating = $this->playerData['defaultRating'];
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

            $this->player  = $player->toArray();
            $this->rating  = $player->rating;
            $this->mom     = $player->mom;
            $this->canRate = $player->canRate;
        } catch (Exception $e) {
            dd($e);
        }
    }
}