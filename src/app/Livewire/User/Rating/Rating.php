<?php declare(strict_types=1);

namespace App\Livewire\User\Rating;

use Livewire\Component;

use App\Livewire\User\Lineups\PlayerTrait;


class Rating extends Component
{    
    public array $player;
    public string $fixtureId;

    use PlayerTrait;

    public function render()
    {
        return view('livewire.user.rating.rating');
    }
}