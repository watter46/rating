<?php declare(strict_types=1);

namespace App\Livewire\Lineups;

use Livewire\Component;
use Illuminate\Support\Str;


class PlayerDetail extends Component
{
    public string $fixtureId;
    public array $player;

    public function render()
    {
        return view('livewire.lineups.player-detail');
    }

    // /**
    //  * ラストネームに変換する
    //  *
    //  * @return string
    //  */
    // public function toLastName(): string
    // {
    //     $shortName = Str::afterLast($this->player['name'], ' ');

    //     return $shortName;
    // }
}
