<?php declare(strict_types=1);

namespace App\Livewire\Result;

use Livewire\Attributes\On;
use Livewire\Component;


class RatedPlayer extends Component
{
    public string $name;
    public string $fixtureId;
    public ?float $rating;
    public ?float $defaultRating;
    public array $player;
    public bool $mom;
    public bool $isRated;
    public string $size;


    public function mount()
    {
        $this->dispatchFetchPlayer();

        $this->defaultRating = (float) $this->player['defaultRating'];
    }

    public function render()
    {
        return view('livewire.result.rated-player');
    }
    
    /**
     * Playerを取得するイベントを発行する
     *
     * @return void
     */
    private function dispatchFetchPlayer(): void
    {
        $this->dispatch('fetch-player.'.$this->player['id']);
    }

    /**
     * Playerイベントから値をセットする
     *
     * @param  array $player
     * @return void
     */
    #[On('player-fetched.{player.id}')]    
    public function handlePlayerEvent(array $player): void
    {
        $this->rating  = $player['rating'];
        $this->mom     = $player['mom'];
    }
}