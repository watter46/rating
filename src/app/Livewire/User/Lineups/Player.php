<?php declare(strict_types=1);

namespace App\Livewire\User\Lineups;

use Livewire\Component;


class Player extends Component
{
    public string $fixtureId;
    public array $playerData;

    public string $name;
    public string $size;

    use PlayerTrait;
    
    public function render()
    {
        return view('livewire.user.lineups.player');
    }
}