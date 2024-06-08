<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Livewire\Attributes\On;

use App\Models\Player;


trait PlayerTrait
{
    /**
     * プロパティを更新するイベントを発行する
     *
     * @param  ?Player $player
     * @return void
     */
    public function dispatchPlayerUpdated(?Player $player)
    {
        if (!$player) return;

        $this->dispatch('update-player.'.$player->player_info_id, $player);
    }

    #[On('update-player.{player.player_info_id}')]
    public function update(array $player)
    {
        $this->player['canRate'] = $player['canRate'];
        $this->player['canMom'] = $player['canMom'];
        $this->player['rateCount'] = $player['rate_count'];
        $this->player['ratings']['my']['rating'] = $player['rating'];
        $this->player['ratings']['my']['mom'] = $player['mom'];
    }

    #[On('mom-count-updated')]
    public function updateMomCount(array $player)
    {
        $this->player['momCount'] = $player['momCount'];

        if ($player['exceedMomLimit']) {
            $this->dispatch('mom-button-disabled');
        }
    }
}