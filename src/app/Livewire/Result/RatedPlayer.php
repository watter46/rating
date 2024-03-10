<?php declare(strict_types=1);

namespace App\Livewire\Result;

use App\Livewire\Lineups\PlayerTrait;
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
        return view('livewire.result.rated-player');
    }
}