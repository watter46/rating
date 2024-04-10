<?php declare(strict_types=1);

namespace App\Livewire\User\Result;

use App\Livewire\User\Lineups\PlayerTrait;
use Livewire\Component;


class RatedPlayer extends Component
{
    public string $name;
    public string $size;
    public string $fixtureId;
    public array $player;

    use PlayerTrait;

    public function render()
    {
        return view('livewire.user.result.rated-player');
    }
}