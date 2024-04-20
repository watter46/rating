<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Exception;
use Livewire\Attributes\On;

use App\Livewire\MessageType;
use App\Models\Player;
use App\UseCases\User\Player\FetchPlayerUseCase;
use App\UseCases\User\PlayerInFixtureRequest;


trait PlayerTrait
{
    public ?float $rating;
    public bool $mom;
    public bool $canRate;
    public bool $canMom;
    public int $rateCount;
    public int $rateLimit;

    private readonly FetchPlayerUseCase $fetchPlayer;
    
    public function bootPlayerTrait(FetchPlayerUseCase $fetchPlayer)
    {
        $this->fetchPlayer = $fetchPlayer;
    }

    public function mountPlayerTrait()
    {
        $this->replace($this->player);
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
                
            $this->replace($player);
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }

    private function replace(Player $player): void
    {
        $this->rating = $player->rating;
        $this->mom    = $player->mom;
        $this->canRate = $player->canRate;
        $this->canMom  = $player->canMom;
        $this->rateCount = $player->rate_count;
        $this->rateLimit = $player->rateLimit;
    }
}