<?php declare(strict_types=1);

namespace App\Livewire\Rating;

use Livewire\Component;

use App\Livewire\Lineups\PlayerTrait;


class Rating extends Component
{    
    public array $player;
    public string $fixtureId;

    use PlayerTrait;

    public function render()
    {
        return view('livewire.rating.rating');
    }
}