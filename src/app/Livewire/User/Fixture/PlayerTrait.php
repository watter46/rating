<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Livewire\Attributes\On;

use App\UseCases\User\Player\FindPlayer;


trait PlayerTrait
{
    public ?float $rating;
    public bool $mom;
    public bool $canRate;
    public bool $canMom;
    public int $rateCount;
    public int $rateLimit;

    private readonly FindPlayer $findPlayer;
    
    public function bootPlayerTrait(FindPlayer $findPlayer)
    {
        $this->findPlayer = $findPlayer;
    }

    public function mountPlayerTrait()
    {
        $this->update($this->player->toArray());
    }

    #[On('update-player.{playerData.id}')]
    public function update(array $player)
    {
        $this->rating = $player['rating'];
        $this->mom    = $player['mom'];
        $this->canRate = $player['canRate'];
        $this->canMom  = $player['canMom'];
        $this->rateCount = $player['rate_count'];
        $this->rateLimit = $player['rateLimit'];
    }
}